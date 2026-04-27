<?php

namespace TitanZero\FilamentChatbot\Drivers;

use Illuminate\Support\Facades\Http;
use TitanZero\FilamentChatbot\Contracts\LlmDriverContract;

/**
 * OpenAI-compatible driver.
 *
 * Works with OpenAI, Azure OpenAI, Mistral, Groq, Ollama, and any endpoint
 * that implements the OpenAI Chat Completions API.
 *
 * Connection config keys:
 *   - url      Base URL (e.g. https://api.openai.com/v1/)
 *   - api_key  Bearer token
 */
class OpenAiDriver implements LlmDriverContract
{
    public function chat(array $messages, array $tools, string $model, array $connection): array
    {
        $payload = [
            'model'    => $model,
            'messages' => $messages,
        ];

        if (! empty($tools)) {
            $payload['tools']       = $tools;
            $payload['tool_choice'] = 'auto';
        }

        $response = Http::withToken($connection['api_key'] ?? '')
            ->baseUrl(rtrim($connection['url'] ?? 'https://api.openai.com/v1/', '/'))
            ->post('/chat/completions', $payload);

        return $response->throw()->json();
    }
}
