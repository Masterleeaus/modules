<?php

namespace TitanZero\FilamentChatbot\Contracts;

interface LlmDriverContract
{
    /**
     * Send a chat completion request and return the raw response array.
     *
     * @param  array<int, array<string, mixed>>  $messages  Chat history including system prompt
     * @param  array<int, array<string, mixed>>  $tools     OpenAI-style tool definitions (may be empty)
     * @param  string  $model                               Model identifier
     * @param  array<string, mixed>  $connection            Connection config (url, api_key, + driver-specific keys)
     *
     * @return array<string, mixed>  Normalised response:
     *                               - choices[0][message][content]        string|null
     *                               - choices[0][message][tool_calls]     array|null
     *                               - choices[0][finish_reason]           string
     *                               - usage[prompt_tokens]                int|null
     *                               - usage[completion_tokens]            int|null
     *                               - model                               string
     */
    public function chat(array $messages, array $tools, string $model, array $connection): array;
}
