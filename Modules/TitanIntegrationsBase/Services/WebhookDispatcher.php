<?php

namespace Modules\TitanIntegrations\Services;

use Illuminate\Support\Facades\Http;
use Modules\TitanIntegrations\Entities\IntegrationLog;
use Modules\TitanIntegrations\Entities\WebhookEndpoint;
use Modules\TitanIntegrations\Jobs\RetryWebhookJob;

class WebhookDispatcher
{
    /**
     * Fire an event to all registered webhook endpoints for a company.
     *
     * @param int    $companyId
     * @param string $event      e.g. 'booking.created'
     * @param array  $payload    event data
     */
    public function fire(int $companyId, string $event, array $payload): void
    {
        $endpoints = WebhookEndpoint::where('company_id', $companyId)
            ->where('is_active', true)
            ->get()
            ->filter(fn($ep) => $ep->listensTo($event));

        foreach ($endpoints as $endpoint) {
            $this->dispatch($endpoint, $event, $payload);
        }
    }

    public function dispatch(WebhookEndpoint $endpoint, string $event, array $payload, int $attempt = 1): void
    {
        $body      = json_encode(['event' => $event, 'data' => $payload, 'timestamp' => now()->toIso8601String()]);
        $signature = $endpoint->sign($body);

        $log = IntegrationLog::create([
            'company_id' => $endpoint->company_id,
            'provider'   => 'webhook',
            'direction'  => 'outbound',
            'event_type' => $event,
            'payload'    => $payload,
            'status'     => 'pending',
            'attempts'   => $attempt,
        ]);

        try {
            $response = Http::timeout(config('titanintegrations.webhooks.timeout', 10))
                ->withHeaders([
                    'Content-Type'                    => 'application/json',
                    'X-TitanIntegrations-Event'       => $event,
                    'X-TitanIntegrations-Signature'   => 'sha256=' . $signature,
                    'X-TitanIntegrations-Delivery'    => (string) $log->id,
                ])
                ->send('POST', $endpoint->url, ['body' => $body]);

            $log->update([
                'status'       => $response->successful() ? 'success' : 'failed',
                'http_status'  => $response->status(),
                'error_message' => $response->successful() ? null : substr($response->body(), 0, 500),
                'processed_at' => now(),
            ]);

            if ($response->successful()) {
                $endpoint->update(['last_triggered_at' => now()]);
            } elseif ($attempt < config('titanintegrations.webhooks.retry_attempts', 3)) {
                $this->scheduleRetry($endpoint, $event, $payload, $attempt);
            }

        } catch (\Throwable $e) {
            $log->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
                'processed_at'  => now(),
            ]);

            if ($attempt < config('titanintegrations.webhooks.retry_attempts', 3)) {
                $this->scheduleRetry($endpoint, $event, $payload, $attempt);
            }
        }
    }

    private function scheduleRetry(WebhookEndpoint $endpoint, string $event, array $payload, int $attempt): void
    {
        $delays = config('titanintegrations.webhooks.retry_delays', [5, 30, 300]);
        $delay  = $delays[$attempt - 1] ?? 300;

        RetryWebhookJob::dispatch($endpoint->id, $event, $payload, $attempt + 1)
            ->delay(now()->addSeconds($delay));
    }
}
