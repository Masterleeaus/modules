<?php

namespace Modules\TitanIntegrations\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Modules\TitanIntegrations\Entities\Integration;
use Modules\TitanIntegrations\Services\OAuthService;

/**
 * QuickBooks Online via Intuit OAuth 2.0.
 */
class QuickBooksIntegration
{
    private const BASE_URL    = 'https://quickbooks.api.intuit.com/v3/company';
    private const SANDBOX_URL = 'https://sandbox-quickbooks.api.intuit.com/v3/company';

    public function __construct(protected OAuthService $oauth) {}

    public function getProvider(): string { return 'quickbooks'; }

    public function testConnection(Integration $integration): array
    {
        $token     = $this->getValidToken($integration);
        $realmId   = $integration->settings['realm_id'] ?? null;
        if (!$realmId) return ['ok' => false, 'error' => 'No QuickBooks Company ID (realm ID) stored'];

        $base     = $this->baseUrl();
        $response = Http::withToken($token)
            ->withHeaders(['Accept' => 'application/json'])
            ->get("{$base}/{$realmId}/companyinfo/{$realmId}");

        return $response->successful()
            ? ['ok' => true, 'account' => $response->json('CompanyInfo.CompanyName', 'QuickBooks')]
            : ['ok' => false, 'error' => $response->json('Fault.Error.0.Message', 'Connection failed')];
    }

    public function syncCustomer(Integration $integration, array $client): ?string
    {
        $token   = $this->getValidToken($integration);
        $realmId = $integration->settings['realm_id'] ?? null;
        if (!$realmId) return null;

        $response = Http::withToken($token)
            ->withHeaders(['Accept' => 'application/json'])
            ->post("{$this->baseUrl()}/{$realmId}/customer", [
                'DisplayName'  => $client['name'],
                'PrimaryEmailAddr' => $client['email'] ? ['Address' => $client['email']] : null,
                'PrimaryPhone' => $client['phone'] ? ['FreeFormNumber' => $client['phone']] : null,
            ]);

        return $response->successful()
            ? $response->json('Customer.Id')
            : null;
    }

    public function syncInvoice(Integration $integration, array $invoice): ?string
    {
        $token   = $this->getValidToken($integration);
        $realmId = $integration->settings['realm_id'] ?? null;
        if (!$realmId) return null;

        $lines = collect($invoice['items'] ?? [])->map(fn($item, $i) => [
            'LineNum'        => $i + 1,
            'Amount'         => $item['quantity'] * $item['unit_price'],
            'DetailType'     => 'SalesItemLineDetail',
            'SalesItemLineDetail' => [
                'Qty'       => $item['quantity'],
                'UnitPrice' => $item['unit_price'],
            ],
            'Description' => $item['description'],
        ])->values()->toArray();

        $response = Http::withToken($token)
            ->withHeaders(['Accept' => 'application/json'])
            ->post("{$this->baseUrl()}/{$realmId}/invoice", [
                'CustomerRef' => ['value' => $invoice['qb_customer_id']],
                'DueDate'     => $invoice['due_date'],
                'DocNumber'   => $invoice['invoice_number'],
                'Line'        => $lines,
            ]);

        return $response->successful()
            ? $response->json('Invoice.Id')
            : null;
    }

    private function baseUrl(): string
    {
        return config('titanintegrations.integrations.quickbooks.sandbox', false)
            ? self::SANDBOX_URL
            : self::BASE_URL;
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
