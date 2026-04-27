<?php

namespace Modules\TitanIntegrations\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\TitanIntegrations\Entities\WebhookEndpoint;
use Modules\TitanIntegrations\Services\WebhookDispatcher;

class RetryWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int    $endpointId,
        private string $event,
        private array  $payload,
        private int    $attempt,
    ) {}

    public function handle(WebhookDispatcher $dispatcher): void
    {
        $endpoint = WebhookEndpoint::find($this->endpointId);
        if (!$endpoint || !$endpoint->is_active) return;

        $dispatcher->dispatch($endpoint, $this->event, $this->payload, $this->attempt);
    }
}
