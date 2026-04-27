<?php

namespace TitanZero\FilamentChatbot\Drivers;

use Illuminate\Support\Facades\Http;
use TitanZero\FilamentChatbot\Contracts\LlmDriverContract;

/**
 * Anthropic Claude driver.
 *
 * Maps OpenAI-style messages to the Anthropic Messages API format and
 * normalises the response back into the OpenAI-style structure expected
 * by RunProcessorService.
 *
 * Connection config keys:
 *   - url              Base URL (default: https://api.anthropic.com)
 *   - api_key          Anthropic API key (x-api-key header)
 *   - anthropic_version  API version header (default: 2023-06-01)
 *
 * Supported models: claude-3-5-sonnet-20241022, claude-3-opus-20240229, etc.
 */
class AnthropicDriver implements LlmDriverContract
{
    private const DEFAULT_URL     = 'https://api.anthropic.com';
    private const DEFAULT_VERSION = '2023-06-01';
    private const DEFAULT_MAX_TOKENS = 4096;

    public function chat(array $messages, array $tools, string $model, array $connection): array
    {
        $baseUrl           = rtrim($connection['url'] ?? self::DEFAULT_URL, '/');
        $apiKey            = $connection['api_key'] ?? '';
        $anthropicVersion  = $connection['anthropic_version'] ?? self::DEFAULT_VERSION;
        $maxTokens         = $connection['max_tokens'] ?? self::DEFAULT_MAX_TOKENS;

        // Anthropic separates the system prompt from the message array
        $systemPrompt = '';
        $chatMessages = [];

        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                $systemPrompt = $msg['content'];
                continue;
            }

            if ($msg['role'] === 'tool') {
                // Tool result — append as a user turn with tool_result content block
                $chatMessages[] = [
                    'role'    => 'user',
                    'content' => [
                        [
                            'type'         => 'tool_result',
                            'tool_use_id'  => $msg['tool_call_id'] ?? '',
                            'content'      => $msg['content'],
                        ],
                    ],
                ];
                continue;
            }

            if (isset($msg['tool_calls'])) {
                // Assistant turn that requested tool calls
                $contentBlocks = [];

                if (! empty($msg['content'])) {
                    $contentBlocks[] = ['type' => 'text', 'text' => $msg['content']];
                }

                foreach ($msg['tool_calls'] as $tc) {
                    $contentBlocks[] = [
                        'type'  => 'tool_use',
                        'id'    => $tc['id'],
                        'name'  => $tc['function']['name'] ?? '',
                        'input' => json_decode($tc['function']['arguments'] ?? '{}', true) ?? [],
                    ];
                }

                $chatMessages[] = ['role' => 'assistant', 'content' => $contentBlocks];
                continue;
            }

            $chatMessages[] = ['role' => $msg['role'], 'content' => $msg['content'] ?? ''];
        }

        $payload = [
            'model'      => $model,
            'max_tokens' => $maxTokens,
            'messages'   => $chatMessages,
        ];

        if ($systemPrompt !== '') {
            $payload['system'] = $systemPrompt;
        }

        if (! empty($tools)) {
            $payload['tools'] = array_map(fn ($t) => [
                'name'         => $t['function']['name'] ?? '',
                'description'  => $t['function']['description'] ?? '',
                'input_schema' => $t['function']['parameters'] ?? ['type' => 'object', 'properties' => []],
            ], $tools);
        }

        $response = Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => $anthropicVersion,
            'Content-Type'      => 'application/json',
        ])
            ->baseUrl($baseUrl)
            ->post('/v1/messages', $payload)
            ->throw()
            ->json();

        return $this->normalise($response);
    }

    /**
     * Convert Anthropic response into the OpenAI-compatible structure.
     *
     * @param  array<string, mixed>  $response
     *
     * @return array<string, mixed>
     */
    private function normalise(array $response): array
    {
        $content    = $response['content'] ?? [];
        $stopReason = $response['stop_reason'] ?? 'end_turn';

        $textContent = '';
        $toolCalls   = [];

        foreach ($content as $block) {
            if (($block['type'] ?? '') === 'text') {
                $textContent .= $block['text'];
            } elseif (($block['type'] ?? '') === 'tool_use') {
                $toolCalls[] = [
                    'id'       => $block['id'],
                    'type'     => 'function',
                    'function' => [
                        'name'      => $block['name'],
                        'arguments' => json_encode($block['input'] ?? []),
                    ],
                ];
            }
        }

        $finishReason = $stopReason === 'tool_use' ? 'tool_calls' : 'stop';

        $message = ['role' => 'assistant', 'content' => $textContent ?: null];

        if (! empty($toolCalls)) {
            $message['tool_calls'] = $toolCalls;
        }

        return [
            'choices' => [
                [
                    'message'       => $message,
                    'finish_reason' => $finishReason,
                ],
            ],
            'usage' => [
                'prompt_tokens'     => $response['usage']['input_tokens'] ?? null,
                'completion_tokens' => $response['usage']['output_tokens'] ?? null,
            ],
            'model' => $response['model'] ?? '',
        ];
    }
}
