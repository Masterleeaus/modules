<?php

namespace Modules\InstantAds\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\InstantAds\Entities\InstantAdsBrandKit;

class AdCopyService
{
    private const OPENAI_ENDPOINT = 'https://api.openai.com/v1/chat/completions';

    /**
     * Generate ad copy from a freeform brief.
     *
     * @return array{headline: string, body: string, cta: string}
     */
    public function generateCopy(string $brief, string $format, ?string $brandKitTagline = null): array
    {
        $taglineHint = $brandKitTagline ? " Brand tagline: \"{$brandKitTagline}\"." : '';

        $systemPrompt = 'You are an expert direct-response copywriter specialising in cleaning industry marketing. '
            . 'Return ONLY a valid JSON object with keys: headline, body, cta. No extra text.';

        $userPrompt = "Write ad copy for the following cleaning service brief.\n"
            . "Brief: {$brief}\n"
            . "Format: {$format}.{$taglineHint}\n"
            . 'Respond with JSON only: {"headline":"...","body":"...","cta":"..."}';

        $apiKey = config('instantads.openai_api_key');

        if (! $apiKey) {
            return $this->fallbackCopy($brief);
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post(self::OPENAI_ENDPOINT, [
                    'model'       => 'gpt-4o',
                    'temperature' => 0.7,
                    'messages'    => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $userPrompt],
                    ],
                ]);

            if ($response->failed()) {
                Log::warning('InstantAds AdCopyService: OpenAI request failed', [
                    'status' => $response->status(),
                ]);

                return $this->fallbackCopy($brief);
            }

            $content = $response->json('choices.0.message.content', '');
            $decoded = json_decode($content, true);

            if (is_array($decoded) && isset($decoded['headline'], $decoded['body'], $decoded['cta'])) {
                return $decoded;
            }

            return $this->fallbackCopy($brief);
        } catch (\Throwable $e) {
            Log::error('InstantAds AdCopyService: exception', ['error' => $e->getMessage()]);

            return $this->fallbackCopy($brief);
        }
    }

    /**
     * Build a cleaning-specific brief and generate ad copy.
     *
     * @return array{headline: string, body: string, cta: string}
     */
    public function generateCleaningCopy(string $jobType, string $season, InstantAdsBrandKit $kit): array
    {
        $brief = "Professional {$jobType} cleaning service. "
            . "Season: {$season}. "
            . "Brand: {$kit->name}. "
            . "Colours: {$kit->primary_color} primary, {$kit->secondary_color} secondary. "
            . ($kit->tagline ? "Tagline: \"{$kit->tagline}\"." : '');

        return $this->generateCopy($brief, 'social_media_ad', $kit->tagline);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function fallbackCopy(string $brief): array
    {
        return [
            'headline' => 'Professional Cleaning You Can Trust',
            'body'     => 'Expert cleaning services tailored to your needs. Book today for a spotless result.',
            'cta'      => 'Book Now',
        ];
    }
}
