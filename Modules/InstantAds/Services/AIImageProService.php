<?php

namespace Modules\InstantAds\Services;

use Illuminate\Support\Facades\Log;
use Modules\InstantAds\Jobs\GenerateAdImageJob;
use Modules\InstantAds\Models\AiImagePro;

class AIImageProService
{
    /**
     * Return the default fallback model list when EntityEnum is unavailable.
     */
    private static function defaultModels(): array
    {
        return [
            'dall-e-3' => [
                'slug'   => 'dall-e-3',
                'label'  => 'DALL-E 3',
                'engine' => 'openai',
            ],
            'stable-diffusion-xl' => [
                'slug'   => 'stable-diffusion-xl',
                'label'  => 'Stable Diffusion XL',
                'engine' => 'stable_diffusion',
            ],
            'flux-pro' => [
                'slug'   => 'flux-pro',
                'label'  => 'Flux Pro',
                'engine' => 'fal_ai',
            ],
        ];
    }

    /**
     * Get active/enabled image models.
     * Uses EntityEnum when available; falls back to the default list.
     */
    public static function getActiveImageModels(): array
    {
        if (! class_exists(\App\Domains\Entity\Enums\EntityEnum::class)) {
            return self::defaultModels();
        }

        try {
            $imageModels = [];

            // EntityEnum-based model slugs that are image-generation capable
            $candidates = [
                'dall-e-2',
                'dall-e-3',
                'gpt-image-1',
                'stable-diffusion-xl',
                'stable-diffusion-xl-1024-v1-0',
                'flux-pro',
                'flux-1-schnell',
                'flux-dev',
            ];

            foreach ($candidates as $slug) {
                try {
                    $enum = \App\Domains\Entity\Enums\EntityEnum::from($slug);
                    $imageModels[$slug] = [
                        'slug'   => $slug,
                        'label'  => $enum->label() ?? $slug,
                        'engine' => method_exists($enum, 'engine') ? ($enum->engine()?->slug() ?? '') : '',
                    ];
                } catch (\Throwable) {
                    // Enum value doesn't exist — skip
                }
            }

            return empty($imageModels) ? self::defaultModels() : $imageModels;
        } catch (\Throwable $e) {
            Log::warning('InstantAds: could not resolve EntityEnum models, using defaults', [
                'error' => $e->getMessage(),
            ]);

            return self::defaultModels();
        }
    }

    /**
     * Create a new AiImagePro record and dispatch the generation job.
     *
     * @return int The new record's ID
     */
    public function dispatchImageGenerationJob(array $params, ?int $userId, mixed $driver): int
    {
        $record = AiImagePro::create([
            'user_id'  => $userId,
            'guest_ip' => $userId ? null : request()->ip(),
            'model'    => $params['model'] ?? 'dall-e-3',
            'engine'   => $params['engine'] ?? '',
            'prompt'   => $params['prompt'],
            'params'   => $params,
            'status'   => 'pending',
        ]);

        GenerateAdImageJob::dispatch($record->id, $userId, $driver);

        return $record->id;
    }
}
