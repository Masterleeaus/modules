<?php

namespace Modules\InstantAds\Services;

use Modules\InstantAds\Models\AiImagePro;

class AIChatImageService
{
    /**
     * Embed a generated ad image into an AI chat message via TitanZero.
     */
    public function embedInChat(AiImagePro $image, string $sessionId = null): array
    {
        if (! class_exists(\Modules\TitanZero\Services\ZeroGateway::class)) {
            return ['ok' => false, 'reason' => 'TitanZero not available'];
        }

        try {
            $gateway = app(\Modules\TitanZero\Services\ZeroGateway::class);
            $images  = $image->generated_images ?? [];
            $result  = $gateway->ingestSignal([
                'type'       => 'instant_ads.image_generated',
                'image_urls' => $images,
                'prompt'     => $image->prompt,
                'model'      => $image->model,
                'session_id' => $sessionId,
            ]);

            return ['ok' => true, 'result' => $result];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
