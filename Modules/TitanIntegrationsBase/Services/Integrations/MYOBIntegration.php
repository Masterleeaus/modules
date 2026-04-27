<?php

namespace Modules\TitanIntegrations\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Modules\TitanIntegrations\Entities\Integration;
use Modules\TitanIntegrations\Services\OAuthService;

/**
 * MYOB AccountRight / MYOB Essentials — Australian accounting integration.
 */
class MYOBIntegration
{
    private const BASE_URL = 'https://api.myob.com/accountright';

    public function __construct(protected OAuthService $oauth) {}

    public function getProvider(): string { return 'myob'; }

    public function testConnection(Integration $integration): array
    {
        $token = $this->getValidToken($integration);

        $response = Http::withToken($token)
            ->get(self::BASE_URL . '/');

        if ($response->successful()) {
            $companies = $response->json();
            $company   = is_array($companies) && !empty($companies)
                ? ($companies[0]['Name'] ?? 'MYOB')
                : 'MYOB AccountRight';

            // Store the company file URI for subsequent requests
            if (is_array($companies) && !empty($companies)) {
                $settings = $integration->settings ?? [];
                $settings['company_file_uri'] = $companies[0]['Uri'] ?? null;
                $settings['company_file_id']  = $companies[0]['Id']  ?? null;
                $integration->settings = $settings;
                $integration->save();
            }

            return ['ok' => true, 'account' => $company];
        }

        return ['ok' => false, 'error' => $response->json('Errors.0.Message', 'MYOB connection failed')];
    }

    /**
     * Create or update a customer (Contact) in MYOB.
     *
     * @param  array  $client  WorkSuite client data
     * @return string|null  MYOB Contact UID on success, null on failure
     */
    public function syncCustomer(Integration $integration, array $client): ?string
    {
        $token   = $this->getValidToken($integration);
        $fileUri = $this->getCompanyFileUri($integration);
        if (!$fileUri) return null;

        $response = Http::withToken($token)
            ->post("{$fileUri}/Contact/Customer", [
                'IsIndividual' => false,
                'CompanyName'  => $client['name'],
                'Addresses'    => [[
                    'Type'  => 'Location',
                    'Email' => $client['email'] ?? null,
                    'Phone1' => $client['phone'] ?? null,
                ]],
            ]);

        return $response->successful()
            ? ($response->header('Location') ? basename($response->header('Location')) : null)
            : null;
    }

    /**
     * Create an invoice in MYOB.
     *
     * @param  array  $invoice  WorkSuite invoice data
     * @return string|null  MYOB Invoice UID on success, null on failure
     */
    public function syncInvoice(Integration $integration, array $invoice): ?string
    {
        $token   = $this->getValidToken($integration);
        $fileUri = $this->getCompanyFileUri($integration);
        if (!$fileUri) return null;

        $lines = collect($invoice['items'] ?? [])->map(fn($item) => [
            'Type'        => 'Transaction',
            'Description' => $item['description'],
            'UnitCount'   => $item['quantity'],
            'UnitPrice'   => $item['unit_price'],
            'Total'       => $item['quantity'] * $item['unit_price'],
        ])->values()->toArray();

        $response = Http::withToken($token)
            ->post("{$fileUri}/Sale/Invoice/Service", [
                'Date'     => $invoice['date']         ?? now()->toDateString(),
                'DueDate'  => $invoice['due_date']     ?? null,
                'Number'   => $invoice['invoice_number'] ?? null,
                'Customer' => ['UID' => $invoice['myob_customer_uid']],
                'Lines'    => $lines,
            ]);

        if ($response->successful()) {
            $location = $response->header('Location');
            return $location ? basename($location) : null;
        }

        return null;
    }

    private function getCompanyFileUri(Integration $integration): ?string
    {
        return $integration->settings['company_file_uri'] ?? null;
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
