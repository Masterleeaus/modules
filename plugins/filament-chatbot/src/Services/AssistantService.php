<?php

namespace TitanZero\FilamentChatbot\Services;

use Illuminate\Support\Facades\Http;
use TitanZero\FilamentChatbot\Contracts\LlmDriverContract;
use TitanZero\FilamentChatbot\Drivers\AnthropicDriver;
use TitanZero\FilamentChatbot\Drivers\GeminiDriver;
use TitanZero\FilamentChatbot\Drivers\OpenAiDriver;
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
     * Resolve the LLM connection config for the given connection name.
     *
     * @return array<string, mixed>
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
     * Resolve the LLM driver instance for a given connection config.
     *
     * The driver is determined by the optional "driver" key in the connection:
     *   - "openai"    (default) → OpenAiDriver
     *   - "anthropic"           → AnthropicDriver
     *   - "gemini"              → GeminiDriver
     * Any custom class name implementing LlmDriverContract is also accepted.
     */
    public function resolveDriver(array $connection): LlmDriverContract
    {
        $driverKey = $connection['driver'] ?? 'openai';

        return match ($driverKey) {
            'openai'    => app(OpenAiDriver::class),
            'anthropic' => app(AnthropicDriver::class),
            'gemini'    => app(GeminiDriver::class),
            default     => class_exists($driverKey)
                ? app($driverKey)
                : app(OpenAiDriver::class),
        };
    }

    /**
     * Build the messages array to send to the LLM.
     *
     * Includes the system prompt, optional page context, the (possibly truncated)
     * conversation history from completed runs, and the current user input.
     *
     * History is truncated to the newest `max_context_messages` message-pairs when
     * the budget is configured (see assistant.php → max_context_messages).
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

        // Apply token budget: keep only the newest N message-pairs from history
        $maxContextMessages = (int) ($assistantConfig['max_context_messages']
            ?? config('assistant.max_context_messages', 0));

        if ($maxContextMessages > 0) {
            $history = $this->truncateHistory($history, $maxContextMessages);
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
     * Dispatch a completion request to the correct LLM driver.
     *
     * @param  array<int, array<string, string>>  $messages
     * @param  array<int, array<string, mixed>>   $tools
     * @param  array<string, mixed>               $llmConnection
     *
     * @return array<string, mixed>  Normalised OpenAI-style response body
     */
    public function callLlm(array $messages, array $tools, array $llmConnection, string $model): array
    {
        $driver = $this->resolveDriver($llmConnection);

        return $driver->chat($messages, $tools, $model, $llmConnection);
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

    // -------------------------------------------------------------------------

    /**
     * Truncate history to at most $maxMessages, keeping the newest pairs.
     *
     * History is stored as alternating user/assistant messages. We trim from
     * the oldest end and always remove messages in pairs to maintain alignment.
     *
     * @param  array<array<string, string>>  $history
     *
     * @return array<array<string, string>>
     */
    private function truncateHistory(array $history, int $maxMessages): array
    {
        if (count($history) <= $maxMessages) {
            return $history;
        }

        $excess = count($history) - $maxMessages;

        // Round up to keep pairs (each pair = 1 user + 1 assistant message)
        if ($excess % 2 !== 0) {
            $excess++;
        }

        return array_values(array_slice($history, $excess));
    }
}

