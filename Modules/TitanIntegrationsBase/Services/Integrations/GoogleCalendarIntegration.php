<?php

namespace Modules\TitanIntegrations\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Modules\TitanIntegrations\Entities\Integration;
use Modules\TitanIntegrations\Services\OAuthService;

class GoogleCalendarIntegration
{
    public function __construct(protected OAuthService $oauth) {}

    public function getProvider(): string { return 'google_calendar'; }

    public function testConnection(Integration $integration): array
    {
        $token = $integration->getDecryptedAccessToken();
        if (!$token) return ['ok' => false, 'error' => 'No access token'];

        if ($integration->isTokenExpired()) {
            $this->oauth->refreshToken($integration);
            $token = $integration->fresh()->getDecryptedAccessToken();
        }

        $response = Http::withToken($token)
            ->get('https://www.googleapis.com/calendar/v3/users/me/calendarList');

        if ($response->successful()) {
            $settings = $integration->settings ?? [];
            $accountName = $settings['account_name'] ?? ($response->json('items.0.id') ?? 'Google Calendar');
            return ['ok' => true, 'account' => $accountName];
        }

        return ['ok' => false, 'error' => $response->json('error.message', 'Connection failed')];
    }

    /**
     * Push a booking (task) to Google Calendar as an event.
     */
    public function createEvent(Integration $integration, array $booking): ?string
    {
        $token      = $this->getValidToken($integration);
        $calendarId = $integration->settings['calendar_id'] ?? 'primary';

        $response = Http::withToken($token)
            ->post("https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events", [
                'summary'     => $booking['title'] ?? 'Booking',
                'description' => $booking['description'] ?? '',
                'start'       => ['dateTime' => $booking['start_at'], 'timeZone' => $booking['timezone'] ?? 'UTC'],
                'end'         => ['dateTime' => $booking['end_at'],   'timeZone' => $booking['timezone'] ?? 'UTC'],
                'location'    => $booking['address'] ?? '',
                'extendedProperties' => [
                    'private' => ['worksuite_task_id' => (string) $booking['id']],
                ],
            ]);

        return $response->successful() ? $response->json('id') : null;
    }

    public function updateEvent(Integration $integration, string $googleEventId, array $booking): bool
    {
        $token      = $this->getValidToken($integration);
        $calendarId = $integration->settings['calendar_id'] ?? 'primary';

        return Http::withToken($token)
            ->put("https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events/{$googleEventId}", [
                'summary'  => $booking['title'] ?? 'Booking',
                'start'    => ['dateTime' => $booking['start_at'], 'timeZone' => $booking['timezone'] ?? 'UTC'],
                'end'      => ['dateTime' => $booking['end_at'],   'timeZone' => $booking['timezone'] ?? 'UTC'],
                'location' => $booking['address'] ?? '',
            ])->successful();
    }

    public function deleteEvent(Integration $integration, string $googleEventId): bool
    {
        $token      = $this->getValidToken($integration);
        $calendarId = $integration->settings['calendar_id'] ?? 'primary';

        return Http::withToken($token)
            ->delete("https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events/{$googleEventId}")
            ->successful();
    }

    /**
     * Get all calendars for the connected account (for settings UI picker).
     */
    public function listCalendars(Integration $integration): array
    {
        $token    = $this->getValidToken($integration);
        $response = Http::withToken($token)->get('https://www.googleapis.com/calendar/v3/users/me/calendarList');

        if (!$response->successful()) return [];

        return collect($response->json('items', []))
            ->map(fn($c) => ['id' => $c['id'], 'summary' => $c['summary']])
            ->values()
            ->toArray();
    }

    public function generateIcalFeed(int $companyId): string
    {
        // Returns a signed URL to the iCal endpoint
        $token = hash_hmac('sha256', "ical_{$companyId}", config('app.key'));
        return route('titan-integrations.ical', ['company' => $companyId, 'token' => $token]);
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
