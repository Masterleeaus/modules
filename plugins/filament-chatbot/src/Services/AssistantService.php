<?php

namespace TitanZero\FilamentChatbot\Services;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Facades\Http;
use TitanZero\FilamentChatbot\Models\AssistantRun;
use TitanZero\FilamentChatbot\Models\AssistantThread;

class AssistantService
{
    /**
     * Retrieve or create a thread for the given user + assistant pair.
     */
    public function resolveThread(string $userIdentifier, string $assistantKey): AssistantThread
    {
        return AssistantThread::firstOrCreate(
            ['user_identifier' => $userIdentifier, 'assistant_key' => $assistantKey],
            ['context' => null]
        );
    }

    /**
     * Create a new pending run on the thread and return it.
     */
    public function createRun(AssistantThread $thread, string $input): AssistantRun
    {
        return $thread->runs()->create([
            'status' => AssistantRun::STATUS_PENDING,
            'input'  => $input,
        ]);
    }

    /**
     * Resolve the assistant config for a given key.
     *
     * @return array<string, mixed>
     */
    public function resolveAssistantConfig(string $assistantKey): array
    {
        $assistants = config('assistant.assistants', []);

        if (! isset($assistants[$assistantKey])) {
            $assistantKey = config('assistant.default_assistant', 'default');
        }

        return $assistants[$assistantKey] ?? array_values($assistants)[0] ?? [];
    }

    /**
     * Resolve the LLM HTTP base URL and API key for the given connection name.
     *
     * @return array{url: string, api_key: string}
     */
    public function resolveLlmConnection(string $connectionName): array
    {
        $connections = config('assistant.llm_connections', []);

        return $connections[$connectionName] ?? $connections['openai'] ?? [
            'url'     => 'https://api.openai.com/v1/',
            'api_key' => '',
        ];
    }

    /**
     * Build the messages array to send to the LLM.
     *
     * Includes the system prompt, optional page context, the full conversation
     * history from completed runs on this thread, and the current user input.
     *
     * @param  array<string, mixed>  $assistantConfig
     * @param  array<array<string, string>>  $history  Prior message pairs
     * @param  array<string, mixed>|null  $context    Current page context
     * @param  string  $input                          Latest user message
     *
     * @return array<int, array<string, string>>
     */
    public function buildMessages(
        array $assistantConfig,
        array $history,
        ?array $context,
        string $input
    ): array {
        $instruction = $assistantConfig['instruction'] ?? 'You are a helpful assistant.';

        if ($context) {
            $instruction .= "\n\nCurrent page context:\n" . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $messages = [['role' => 'system', 'content' => $instruction]];

        foreach ($history as $msg) {
            $messages[] = $msg;
        }

        $messages[] = ['role' => 'user', 'content' => $input];

        return $messages;
    }

    /**
     * Collect function/tool definitions from the tools configured for this assistant.
     *
     * @param  array<string>  $toolKeys
     *
     * @return array<int, array<string, mixed>>  OpenAI-style tool definitions
     */
    public function buildToolDefinitions(array $toolKeys): array
    {
        $definitions = [];
        $toolsConfig = config('assistant.tools', []);

        foreach ($toolKeys as $key) {
            if (! isset($toolsConfig[$key])) {
                continue;
            }

            $toolConfig = $toolsConfig[$key];
            $tool       = value($toolConfig['tool'] ?? null);

            if (! $tool || ! method_exists($tool, 'generateFunctionDefinitions')) {
                continue;
            }

            foreach ($tool->generateFunctionDefinitions() as $def) {
                $definitions[] = [
                    'type'     => 'function',
                    'function' => $def,
                ];
            }
        }

        return $definitions;
    }

    /**
     * Dispatch a completion request to the LLM.
     *
     * @param  array<int, array<string, string>>  $messages
     * @param  array<int, array<string, mixed>>   $tools
     * @param  array<string, mixed>               $llmConnection
     *
     * @return array<string, mixed>  Raw response body as an array
     */
    public function callLlm(array $messages, array $tools, array $llmConnection, string $model): array
    {
        $payload = [
            'model'    => $model,
            'messages' => $messages,
        ];

        if (! empty($tools)) {
            $payload['tools']       = $tools;
            $payload['tool_choice'] = 'auto';
        }

        $response = Http::withToken($llmConnection['api_key'])
            ->baseUrl(rtrim($llmConnection['url'], '/'))
            ->post('/chat/completions', $payload);

        return $response->throw()->json();
    }

    /**
     * Execute a tool call and return its string result.
     */
    public function executeTool(string $toolKey, string $functionName, array $arguments): string
    {
        $toolsConfig = config('assistant.tools', []);

        if (! isset($toolsConfig[$toolKey])) {
            return 'Tool not found.';
        }

        $tool = value($toolsConfig[$toolKey]['tool'] ?? null);

        if (! $tool || ! method_exists($tool, $functionName)) {
            return 'Function not available.';
        }

        $result = $tool->$functionName(...array_values($arguments));

        if (is_object($result) && method_exists($result, 'getContent')) {
            return (string) $result->getContent();
        }

        return is_string($result) ? $result : json_encode($result);
    }

    /**
     * Resolve the tool key for a given fully-qualified function name (namespace.function).
     */
    public function toolKeyForFunction(string $functionName): ?string
    {
        $toolsConfig = config('assistant.tools', []);

        foreach ($toolsConfig as $key => $config) {
            $namespace = $config['namespace'] ?? $key;
            if (str_starts_with($functionName, $namespace . '.') || $functionName === $namespace) {
                return $key;
            }
        }

        // Fallback: try matching by bare function name across tool methods
        foreach ($toolsConfig as $key => $config) {
            $tool = value($config['tool'] ?? null);
            if ($tool && method_exists($tool, $functionName)) {
                return $key;
            }
        }

        return null;
    }
}
