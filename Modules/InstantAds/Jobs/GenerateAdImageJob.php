<?php

namespace Modules\InstantAds\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\InstantAds\Models\AiImagePro;
use Throwable;

class GenerateAdImageJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries   = 3;
    public int $timeout = 180;

    public function __construct(
        protected int $recordId,
        protected ?int $userId,
        protected mixed $driver = null
    ) {}

    public function handle(): void
    {
        $record = AiImagePro::find($this->recordId);

        if (! $record) {
            Log::warning('InstantAds: generation record not found', [
                'record_id' => $this->recordId,
                'user_id'   => $this->userId,
            ]);

            $this->refundCredit();
            return;
        }

        try {
            $record->markAsStarted();

            $imageCount = $record->params['image_count'] ?? 1;
            $paths      = [];

            for ($i = 0; $i < $imageCount; $i++) {
                $result = $this->callAiProvider($record);

                if ($result) {
                    $paths[] = $result;
                }
            }

            if (! empty($paths)) {
                $record->markAsCompleted($paths, [
                    'model'  => $record->model,
                    'count'  => count($paths),
                    'params' => $record->params,
                ]);
            } elseif ($record->isPending() || $record->status === 'processing') {
                $record->markAsFailed('No images were generated');
                $this->refundCredit();
            }
        } catch (Throwable $e) {
            $record->markAsFailed($e->getMessage());
            $this->refundCredit();

            Log::error('InstantAds: image generation failed', [
                'error'     => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
                'user_id'   => $this->userId,
                'record_id' => $this->recordId,
            ]);
        }
    }

    private function callAiProvider(AiImagePro $record): ?string
    {
        // Route through TitanZero when available
        if (class_exists(\Modules\TitanZero\Services\ZeroGateway::class)) {
            try {
                /** @var \Modules\TitanZero\Services\ZeroGateway $gateway */
                $gateway = app(\Modules\TitanZero\Services\ZeroGateway::class);
                $result  = $gateway->generateImage([
                    'prompt'       => $record->prompt,
                    'model'        => $record->model,
                    'engine'       => $record->engine,
                    'aspect_ratio' => $record->params['aspect_ratio'] ?? '1:1',
                    'style'        => $record->params['style'] ?? null,
                ]);

                if (! empty($result['url'])) {
                    return $this->downloadAndStore($result['url'], $record->user_id, $record);
                }

                if (! empty($result['b64_json'])) {
                    return $this->storeBase64($result['b64_json'], $record->user_id, $record);
                }
            } catch (Throwable $e) {
                Log::warning('InstantAds: TitanZero generation failed in job', [
                    'error'     => $e->getMessage(),
                    'record_id' => $record->id,
                ]);
            }
        }

        // No provider available — mark as failed
        return null;
    }

    private function downloadAndStore(string $url, ?int $userId, AiImagePro $record): ?string
    {
        try {
            $response = \Illuminate\Support\Facades\Http::get($url);

            if (! $response->successful()) {
                return null;
            }

            $name      = uniqid('img_', true) . '.png';
            $directory = $userId ? "media/images/u-{$userId}" : 'guest';
            $filename  = "{$directory}/{$name}";

            Storage::disk('public')->put($filename, $response->body());
            $record->saveDimensions($filename);

            return "/uploads/{$filename}";
        } catch (Throwable $e) {
            Log::error('InstantAds: failed to download image in job', [
                'url'   => $url,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function storeBase64(string $b64, ?int $userId, AiImagePro $record): ?string
    {
        try {
            $name      = uniqid('img_', true) . '.png';
            $directory = $userId ? "media/images/u-{$userId}" : 'guest';
            $filename  = "{$directory}/{$name}";

            Storage::disk('public')->put($filename, base64_decode($b64));
            $record->saveDimensions($filename);

            return "/uploads/{$filename}";
        } catch (Throwable $e) {
            Log::error('InstantAds: failed to store base64 image in job', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function refundCredit(): void
    {
        if ($this->driver && method_exists($this->driver, 'increaseCredit') && method_exists($this->driver, 'calculate')) {
            try {
                $this->driver->increaseCredit($this->driver->calculate());
            } catch (Throwable) {
                // Silently swallow — credit refund is best-effort
            }
        }
    }
}
