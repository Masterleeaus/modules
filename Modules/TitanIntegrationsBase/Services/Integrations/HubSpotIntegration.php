<?php

namespace Modules\TitanIntegrations\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Modules\TitanIntegrations\Entities\Integration;

class HubSpotIntegration
{
    public function getProvider(): string { return 'hubspot'; }

    public function testConnection(Integration $integration): array
    {
        $key = $integration->getDecryptedApiKey();
        if (!$key) return ['ok' => false, 'error' => 'No API key configured'];

        $response = Http::withToken($key)
            ->get('https://api.hubapi.com/crm/v3/objects/contacts?limit=1');

        return $response->successful()
            ? ['ok' => true, 'account' => 'HubSpot CRM']
            : ['ok' => false, 'error' => $response->json('message', 'Invalid API key')];
    }

    /**
     * Create or update a HubSpot contact from a WorkSuite client.
     */
    public function syncContact(Integration $integration, array $client): ?string
    {
        $key = $integration->getDecryptedApiKey();
        if (!$key) return null;

        $properties = array_filter([
            'email'     => $client['email'] ?? null,
            'firstname' => $client['first_name'] ?? explode(' ', $client['name'] ?? '')[0],
            'lastname'  => $client['last_name']  ?? (explode(' ', $client['name'] ?? '') [1] ?? ''),
            'phone'     => $client['phone'] ?? null,
            'company'   => $client['company_name'] ?? null,
        ]);

        // Try to find existing contact by email first
        if (!empty($client['email'])) {
            $search = Http::withToken($key)
                ->post('https://api.hubapi.com/crm/v3/objects/contacts/search', [
                    'filterGroups' => [[
                        'filters' => [['propertyName' => 'email', 'operator' => 'EQ', 'value' => $client['email']]],
                    ]],
                ]);

            if ($search->json('total', 0) > 0) {
                $contactId = $search->json('results.0.id');
                Http::withToken($key)
                    ->patch("https://api.hubapi.com/crm/v3/objects/contacts/{$contactId}", ['properties' => $properties]);
                return $contactId;
            }
        }

        $response = Http::withToken($key)
            ->post('https://api.hubapi.com/crm/v3/objects/contacts', ['properties' => $properties]);

        return $response->successful() ? $response->json('id') : null;
    }
}
