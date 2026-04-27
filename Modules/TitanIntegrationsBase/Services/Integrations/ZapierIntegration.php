<?php

namespace Modules\TitanIntegrations\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Modules\TitanIntegrations\Entities\Integration;

/**
 * Zapier / Make (Integromat) / n8n — outbound webhook triggers.
 * These are one-way: WorkSuite fires events to Zapier webhook URLs.
 */
class ZapierIntegration
{
    public function getProvider(): string { return 'zapier'; }

    public function testConnection(Integration $integration): array
    {
        $url = $integration->webhook_url;
        if (!$url) return ['ok' => false, 'error' => 'No webhook URL configured'];

        $response = Http::post($url, [
            'event'   => 'ping',
            'message' => 'WorkSuite Zapier integration test',
        ]);

        // Zapier returns 200 even on test hooks
        return $response->status() < 500
            ? ['ok' => true, 'account' => 'Zapier']
            : ['ok' => false, 'error' => 'Webhook unreachable'];
    }

    /**
     * Fire an event payload to Zapier/Make/n8n webhook URL.
     */
    public function fireEvent(Integration $integration, string $event, array $data): bool
    {
        $url = $integration->webhook_url;
        if (!$url || !$integration->isConnected()) return false;

        return Http::post($url, array_merge($data, [
            'event'     => $event,
            'source'    => 'worksuite',
            'timestamp' => now()->toIso8601String(),
        ]))->status() < 500;
    }
}
