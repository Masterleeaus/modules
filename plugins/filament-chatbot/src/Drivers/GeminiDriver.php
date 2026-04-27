<?php

namespace TitanZero\FilamentChatbot\Drivers;

use Illuminate\Support\Facades\Http;
use TitanZero\FilamentChatbot\Contracts\LlmDriverContract;

/**
 * Google Gemini driver (via the generateContent REST API).
 *
 * Maps OpenAI-style messages to the Gemini API format and normalises
 * the response back into the OpenAI-style structure expected by RunProcessorService.
 *
 * Connection config keys:
 *   - url      Base URL (default: https://generativelanguage.googleapis.com/v1beta)
 *   - api_key  Google API key (appended as ?key= query param)
 *
 * Supported models: gemini-1.5-pro, gemini-1.5-flash, gemini-2.0-flash, etc.
 */
class GeminiDriver implements LlmDriverContract
{
    private const DEFAULT_URL = 'https://generativelanguage.googleapis.com/v1beta';

    public function chat(array $messages, array $tools, string $model, array $connection): array
    {
        $baseUrl = rtrim($connection['url'] ?? self::DEFAULT_URL, '/');
        $apiKey  = $connection['api_key'] ?? '';

        $systemInstruction = null;
        $contents          = [];

        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                $systemInstruction = ['parts' => [['text' => $msg['content']]]];
                continue;
            }

            if ($msg['role'] === 'tool') {
                // Tool result
                $contents[] = [
                    'role'  => 'user',
                    'parts' => [
                        [
                            'functionResponse' => [
                                'name'     => '',           // Gemini doesn't require name on response
                                'response' => ['content' => $msg['content']],
                            ],
                        ],
                    ],
                ];
                continue;
            }

            if (isset($msg['tool_calls'])) {
                // Assistant tool call request
                $parts = [];

                if (! empty($msg['content'])) {
                    $parts[] = ['text' => $msg['content']];
                }

                foreach ($msg['tool_calls'] as $tc) {
                    $parts[] = [
                        'functionCall' => [
                            'name' => $tc['function']['name'] ?? '',
                            'args' => json_decode($tc['function']['arguments'] ?? '{}', true) ?? [],
                        ],
                    ];
                }

                $contents[] = ['role' => 'model', 'parts' => $parts];
                continue;
            }

            $geminiRole = $msg['role'] === 'assistant' ? 'model' : 'user';
            $contents[] = ['role' => $geminiRole, 'parts' => [['text' => $msg['content'] ?? '']]];
        }

        $payload = ['contents' => $contents];

        if ($systemInstruction) {
            $payload['systemInstruction'] = $systemInstruction;
        }

        if (! empty($tools)) {
            $payload['tools'] = [
                [
                    'functionDeclarations' => array_map(fn ($t) => [
                        'name'        => $t['function']['name'] ?? '',
                        'description' => $t['function']['description'] ?? '',
                        'parameters'  => $t['function']['parameters'] ?? ['type' => 'object', 'properties' => []],
                    ], $tools),
                ],
            ];
        }

        $endpoint = "{$baseUrl}/models/{$model}:generateContent?key={$apiKey}";

        $response = Http::post($endpoint, $payload)->throw()->json();

        return $this->normalise($response, $model);
    }

    /**
     * Convert Gemini response into the OpenAI-compatible structure.
     *
     * @param  array<string, mixed>  $response
     *
     * @return array<string, mixed>
     */
    private function normalise(array $response, string $model): array
    {
        $candidate  = ($response['candidates'][0] ?? []);
        $parts      = $candidate['content']['parts'] ?? [];
        $finishReason = strtolower($candidate['finishReason'] ?? 'stop');

        $textContent = '';
        $toolCalls   = [];
        $tcIndex     = 0;

        foreach ($parts as $part) {
            if (isset($part['text'])) {
                $textContent .= $part['text'];
            } elseif (isset($part['functionCall'])) {
                $toolCalls[] = [
                    'id'       => 'call_' . $tcIndex++,
                    'type'     => 'function',
                    'function' => [
                        'name'      => $part['functionCall']['name'],
                        'arguments' => json_encode($part['functionCall']['args'] ?? []),
                    ],
                ];
            }
        }

        $openAiFinishReason = match ($finishReason) {
            'max_tokens' => 'length',
            'stop'       => 'stop',
            default      => empty($toolCalls) ? 'stop' : 'tool_calls',
        };

        $message = ['role' => 'assistant', 'content' => $textContent ?: null];

        if (! empty($toolCalls)) {
            $message['tool_calls']  = $toolCalls;
            $openAiFinishReason     = 'tool_calls';
        }

        $usage = $response['usageMetadata'] ?? [];

        return [
            'choices' => [
                [
                    'message'       => $message,
                    'finish_reason' => $openAiFinishReason,
                ],
            ],
            'usage' => [
                'prompt_tokens'     => $usage['promptTokenCount'] ?? null,
                'completion_tokens' => $usage['candidatesTokenCount'] ?? null,
            ],
            'model' => $model,
        ];
    }
}
