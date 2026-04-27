<?php

namespace Modules\InstantAds\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\InstantAds\Models\AiImagePro;
use Throwable;

class RealtimeGenerationService
{
    private const API_URL        = 'https://api.together.xyz/v1/images/generations';
    private const IMAGE_WIDTH    = 1024;
    private const IMAGE_HEIGHT   = 768;
    private const INFERENCE_STEPS = 3;
    private const DEFAULT_MODEL  = 'black-forest-labs/FLUX.1-schnell';

    /**
     * Generate an image synchronously, routing through TitanZero when available.
     */
    public function generate(AiImagePro $record): AiImagePro
    {
        $prompt = $this->buildPrompt($record->prompt, $record->params['style'] ?? null);

        $record->markAsStarted();

        // Try TitanZero gateway first
        if (class_exists(\Modules\TitanZero\Services\ZeroGateway::class)) {
            try {
                /** @var \Modules\TitanZero\Services\ZeroGateway $gateway */
                $gateway = app(\Modules\TitanZero\Services\ZeroGateway::class);
                $result  = $gateway->generateImage([
                    'prompt' => $prompt,
                    'model'  => $record->model ?? self::DEFAULT_MODEL,
                    'width'  => self::IMAGE_WIDTH,
                    'height' => self::IMAGE_HEIGHT,
                    'steps'  => self::INFERENCE_STEPS,
                ]);

                if (! empty($result['url'])) {
                    $storedPath = $this->downloadAndStore($result['url'], $record->user_id);

                    if ($storedPath) {
                        $record->markAsCompleted([$storedPath], ['is_realtime' => true, 'via' => 'titan']);
                        $record->saveDimensions();
                        return $record->refresh();
                    }
                }
            } catch (Throwable $e) {
                Log::warning('InstantAds: TitanZero realtime generation failed, falling back to direct API', [
                    'error'     => $e->getMessage(),
                    'record_id' => $record->id,
                ]);
            }
        }

        // Direct Together API fallback
        return $this->generateViaTogether($record, $prompt);
    }

    private function generateViaTogether(AiImagePro $record, string $prompt): AiImagePro
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getApiKey(),
        ])->post(self::API_URL, [
            'prompt' => $prompt,
            'model'  => self::DEFAULT_MODEL,
            'width'  => self::IMAGE_WIDTH,
            'height' => self::IMAGE_HEIGHT,
            'steps'  => self::INFERENCE_STEPS,
        ]);

        if (! $response->successful()) {
            $error = data_get($response->json(), 'error.message', 'Together API request failed');
            $record->markAsFailed($error);
            return $record;
        }

        $imageUrl = data_get($response->json(), 'data.0.url');

        if (! $imageUrl) {
            $record->markAsFailed('No image URL in API response');
            return $record;
        }

        $storedPath = $this->downloadAndStore($imageUrl, $record->user_id);

        if (! $storedPath) {
            $record->markAsFailed('Failed to download and store generated image');
            return $record;
        }

        $record->markAsCompleted([$storedPath], ['is_realtime' => true, 'via' => 'together']);
        $record->saveDimensions();

        return $record->refresh();
    }

    private function buildPrompt(string $prompt, ?string $style): string
    {
        if ($style && $style !== 'none' && $style !== '') {
            $prompt .= '. Use ' . $style . ' style for the image.';
        }

        return $prompt;
    }

    private function downloadAndStore(string $url, ?int $userId): ?string
    {
        try {
            $response = Http::get($url);

            if (! $response->successful()) {
                return null;
            }

            $fileContent = $response->body();
            $contentType = $response->header('Content-Type') ?? 'image/jpeg';
            $extension   = match (true) {
                str_contains($contentType, 'png')  => 'png',
                str_contains($contentType, 'webp') => 'webp',
                default                            => 'jpg',
            };
            $directory   = $userId ? "media/images/u-{$userId}" : 'media/images/guest';
            $filename    = $directory . '/' . uniqid('rt_', true) . '.' . $extension;

            Storage::disk('public')->put($filename, $fileContent);

            return '/uploads/' . $filename;
        } catch (Throwable $e) {
            Log::error('InstantAds: failed to download and store realtime image', [
                'url'     => $url,
                'user_id' => $userId,
                'error'   => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function getApiKey(): string
    {
        if (function_exists('setting')) {
            return setting('together_api_key', '');
        }

        return config('services.together.api_key', '');
    }
}
