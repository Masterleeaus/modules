<?php

namespace Modules\TitanIntegrations\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Modules\TitanIntegrations\Entities\Integration;

class MailchimpIntegration
{
    public function getProvider(): string { return 'mailchimp'; }

    public function testConnection(Integration $integration): array
    {
        [$key, $dc] = $this->getKeyAndDc($integration);
        if (!$key) return ['ok' => false, 'error' => 'No API key configured'];

        $response = Http::withBasicAuth('anystring', $key)
            ->get("https://{$dc}.api.mailchimp.com/3.0/");

        return $response->successful()
            ? ['ok' => true, 'account' => $response->json('account_name', 'Mailchimp')]
            : ['ok' => false, 'error' => $response->json('detail', 'Invalid API key')];
    }

    public function subscribeContact(Integration $integration, array $client): bool
    {
        [$key, $dc] = $this->getKeyAndDc($integration);
        $listId = $integration->settings['list_id'] ?? null;

        if (!$key || !$listId) return false;

        $hash     = md5(strtolower($client['email'] ?? ''));
        $response = Http::withBasicAuth('anystring', $key)
            ->put("https://{$dc}.api.mailchimp.com/3.0/lists/{$listId}/members/{$hash}", [
                'email_address' => $client['email'],
                'status_if_new' => 'subscribed',
                'merge_fields'  => [
                    'FNAME' => $client['first_name'] ?? '',
                    'LNAME' => $client['last_name']  ?? '',
                    'PHONE' => $client['phone'] ?? '',
                ],
            ]);

        return $response->successful();
    }

    /**
     * Get all available lists/audiences — for settings UI picker.
     */
    public function getLists(Integration $integration): array
    {
        [$key, $dc] = $this->getKeyAndDc($integration);
        if (!$key) return [];

        $response = Http::withBasicAuth('anystring', $key)
            ->get("https://{$dc}.api.mailchimp.com/3.0/lists?count=100");

        return collect($response->json('lists', []))
            ->map(fn($l) => ['id' => $l['id'], 'name' => $l['name']])
            ->values()
            ->toArray();
    }

    private function getKeyAndDc(Integration $integration): array
    {
        $key = $integration->getDecryptedApiKey();
        $dc  = $key ? substr($key, strrpos($key, '-') + 1) : null;
        return [$key, $dc];
    }
}
