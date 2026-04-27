<?php

namespace TitanZero\FilamentChatbot\Services;

use TitanZero\FilamentChatbot\Events\AssistantRunCompleted;
use TitanZero\FilamentChatbot\Models\AssistantRun;

class RunProcessorService
{
    public function __construct(
        protected AssistantService $assistantService,
    ) {}

    /**
     * Process a pending run end-to-end, including tool calls.
     *
     * 1. Mark the run as "processing".
     * 2. Build message history + context.
     * 3. Call the LLM; handle any tool calls (up to $maxIterations rounds).
     * 4. Persist the final output and mark the run "completed".
     * On any exception the run is marked "failed" with the error message.
     */
    public function process(AssistantRun $run, int $maxIterations = 5): void
    {
        $run->update([
            'status'     => AssistantRun::STATUS_PROCESSING,
            'started_at' => now(),
        ]);

        try {
            $thread    = $run->thread;
            $assistant = $this->assistantService->resolveAssistantConfig($thread->assistant_key);
            $llm       = $this->assistantService->resolveLlmConnection($assistant['llm_connection'] ?? 'openai');
            $model     = $assistant['model'] ?? 'gpt-4o';
            $toolKeys  = $assistant['tools'] ?? [];
            $history   = $thread->messageHistory();
            // messageHistory() only returns completed runs, so the current pending run
            // is not included — no additional filtering needed.

            $tools    = $this->assistantService->buildToolDefinitions($toolKeys);
            $messages = $this->assistantService->buildMessages($assistant, $history, $thread->context, $run->input);

            $allToolCalls   = [];
            $allToolResults = [];

            for ($i = 0; $i < $maxIterations; $i++) {
                $response = $this->assistantService->callLlm($messages, $tools, $llm, $model);

                $choice  = $response['choices'][0] ?? null;
                $message = $choice['message'] ?? null;

                if (! $message) {
                    break;
                }

                $finishReason = $choice['finish_reason'] ?? 'stop';

                // If no tool calls, we have our final answer
                if ($finishReason !== 'tool_calls' || empty($message['tool_calls'])) {
                    $output = $message['content'] ?? '';

                    $run->update([
                        'status'        => AssistantRun::STATUS_COMPLETED,
                        'output'        => $output,
                        'messages'      => $messages,
                        'tool_calls'    => $allToolCalls ?: null,
                        'tool_results'  => $allToolResults ?: null,
                        'input_tokens'  => $response['usage']['prompt_tokens'] ?? null,
                        'output_tokens' => $response['usage']['completion_tokens'] ?? null,
                        'model'         => $response['model'] ?? $model,
                        'completed_at'  => now(),
                    ]);

                    AssistantRunCompleted::dispatch($run->fresh());

                    return;
                }

                // Handle tool calls
                $messages[] = $message; // include assistant's tool call request in context

                foreach ($message['tool_calls'] as $toolCall) {
                    $functionName = $toolCall['function']['name'] ?? '';
                    $arguments    = json_decode($toolCall['function']['arguments'] ?? '{}', true) ?? [];
                    $toolKey      = $this->assistantService->toolKeyForFunction($functionName);
                    $result       = $toolKey
                        ? $this->assistantService->executeTool($toolKey, $functionName, $arguments)
                        : 'Tool not found.';

                    $allToolCalls[]   = $toolCall;
                    $allToolResults[] = ['function' => $functionName, 'result' => $result];

                    $messages[] = [
                        'role'         => 'tool',
                        'tool_call_id' => $toolCall['id'],
                        'content'      => $result,
                    ];
                }
            }

            // Exceeded max iterations — use whatever we have
            $run->update([
                'status'       => AssistantRun::STATUS_FAILED,
                'error'        => 'Maximum tool-call iterations reached without a final response.',
                'messages'     => $messages,
                'tool_calls'   => $allToolCalls ?: null,
                'tool_results' => $allToolResults ?: null,
                'completed_at' => now(),
            ]);

            AssistantRunCompleted::dispatch($run->fresh());
        } catch (\Throwable $e) {
            $run->update([
                'status'       => AssistantRun::STATUS_FAILED,
                'error'        => $e->getMessage(),
                'completed_at' => now(),
            ]);

            AssistantRunCompleted::dispatch($run->fresh());
        }
    }
}
