<?php

namespace Modules\TitanIntegrations\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Modules\TitanIntegrations\Entities\Integration;
use Modules\TitanIntegrations\Services\OAuthService;

/**
 * Microsoft Outlook / Microsoft 365 Calendar via Microsoft Graph API.
 */
class OutlookCalendarIntegration
{
    private const GRAPH_BASE = 'https://graph.microsoft.com/v1.0';

    public function __construct(protected OAuthService $oauth) {}

    public function getProvider(): string { return 'outlook_calendar'; }

    public function testConnection(Integration $integration): array
    {
        $token = $this->getValidToken($integration);
        $response = Http::withToken($token)->get(self::GRAPH_BASE . '/me');

        return $response->successful()
            ? ['ok' => true, 'account' => $response->json('userPrincipalName', 'Outlook')]
            : ['ok' => false, 'error' => $response->json('error.message', 'Connection failed')];
    }

    public function createEvent(Integration $integration, array $booking): ?string
    {
        $token      = $this->getValidToken($integration);
        $calendarId = $integration->settings['calendar_id'] ?? null;
        $url        = $calendarId
            ? self::GRAPH_BASE . "/me/calendars/{$calendarId}/events"
            : self::GRAPH_BASE . '/me/events';

        $response = Http::withToken($token)->post($url, [
            'subject' => $booking['title'] ?? 'Booking',
            'body'    => ['contentType' => 'text', 'content' => $booking['description'] ?? ''],
            'start'   => ['dateTime' => $booking['start_at'], 'timeZone' => $booking['timezone'] ?? 'UTC'],
            'end'     => ['dateTime' => $booking['end_at'],   'timeZone' => $booking['timezone'] ?? 'UTC'],
            'location' => ['displayName' => $booking['address'] ?? ''],
            'singleValueExtendedProperties' => [[
                'id'    => 'String {66f5a359-4659-4830-9070-00047ec6ac6e} Name worksuite_task_id',
                'value' => (string) $booking['id'],
            ]],
        ]);

        return $response->successful() ? $response->json('id') : null;
    }

    public function deleteEvent(Integration $integration, string $eventId): bool
    {
        $token = $this->getValidToken($integration);
        return Http::withToken($token)
            ->delete(self::GRAPH_BASE . "/me/events/{$eventId}")
            ->status() === 204;
    }

    public function listCalendars(Integration $integration): array
    {
        $token    = $this->getValidToken($integration);
        $response = Http::withToken($token)->get(self::GRAPH_BASE . '/me/calendars');

        return collect($response->json('value', []))
            ->map(fn($c) => ['id' => $c['id'], 'summary' => $c['name']])
            ->values()->toArray();
    }

    private function getValidToken(Integration $integration): string
    {
        if ($integration->isTokenExpired()) {
            $this->oauth->refreshToken($integration);
            $integration = $integration->fresh();
        }
        return $integration->getDecryptedAccessToken() ?? '';
    }
}
