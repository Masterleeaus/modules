<?php

namespace Modules\TitanIntegrations\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Modules\TitanIntegrations\Entities\Integration;

class SlackIntegration
{
    public function getProvider(): string { return 'slack'; }

    public function testConnection(Integration $integration): array
    {
        $url = $integration->webhook_url;
        if (!$url) return ['ok' => false, 'error' => 'No webhook URL configured'];

        $response = Http::post($url, ['text' => '✅ WorkSuite Slack integration connected successfully!']);
        return $response->successful()
            ? ['ok' => true, 'account' => 'Slack channel']
            : ['ok' => false, 'error' => 'Webhook test failed'];
    }

    public function send(Integration $integration, string $message, array $blocks = []): bool
    {
        $url = $integration->webhook_url;
        if (!$url || !$integration->isConnected()) return false;

        $payload = ['text' => $message];
        if ($blocks) $payload['blocks'] = $blocks;

        return Http::post($url, $payload)->successful();
    }

    public function sendBookingNotification(Integration $integration, array $booking): bool
    {
        return $this->send($integration, '', [
            [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => "*New Booking* #{$booking['id']}\n*Client:* {$booking['client_name']}\n*Service:* {$booking['service']}\n*Date:* {$booking['date']}\n*Cleaner:* {$booking['provider'] ?? 'Unassigned'}",
                ],
            ],
        ]);
    }
}
