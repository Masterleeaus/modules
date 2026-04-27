<?php

namespace Modules\TitanIntegrations\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Modules\TitanIntegrations\Entities\Integration;
use Modules\TitanIntegrations\Services\OAuthService;

class XeroIntegration
{
    public function __construct(protected OAuthService $oauth) {}

    public function getProvider(): string { return 'xero'; }

    public function testConnection(Integration $integration): array
    {
        $token = $this->getValidToken($integration);
        $response = Http::withToken($token)
            ->withHeaders(['Xero-tenant-id' => $this->getTenantId($integration)])
            ->get('https://api.xero.com/api.xro/2.0/Organisation');

        if ($response->successful()) {
            $org = $response->json('Organisations.0.Name', 'Xero');
            return ['ok' => true, 'account' => $org];
        }

        return ['ok' => false, 'error' => $response->json('Detail', 'Xero connection failed')];
    }

    /**
     * Push a WorkSuite client to Xero as a Contact.
     */
    public function syncClient(Integration $integration, array $client): ?string
    {
        $token    = $this->getValidToken($integration);
        $tenantId = $this->getTenantId($integration);

        $response = Http::withToken($token)
            ->withHeaders(['Xero-tenant-id' => $tenantId])
            ->post('https://api.xero.com/api.xro/2.0/Contacts', [
                'Contacts' => [[
                    'Name'         => $client['name'],
                    'EmailAddress' => $client['email'] ?? null,
                    'Phones'       => isset($client['phone']) ? [['PhoneType' => 'DEFAULT', 'PhoneNumber' => $client['phone']]] : [],
                ]],
            ]);

        return $response->successful()
            ? $response->json('Contacts.0.ContactID')
            : null;
    }

    /**
     * Push a WorkSuite invoice to Xero.
     */
    public function syncInvoice(Integration $integration, array $invoice): ?string
    {
        $token    = $this->getValidToken($integration);
        $tenantId = $this->getTenantId($integration);

        $lineItems = collect($invoice['items'] ?? [])->map(fn($item) => [
            'Description' => $item['description'],
            'Quantity'    => $item['quantity'],
            'UnitAmount'  => $item['unit_price'],
            'AccountCode' => '200',
        ])->toArray();

        $response = Http::withToken($token)
            ->withHeaders(['Xero-tenant-id' => $tenantId])
            ->post('https://api.xero.com/api.xro/2.0/Invoices', [
                'Invoices' => [[
                    'Type'        => 'ACCREC',
                    'Contact'     => ['ContactID' => $invoice['xero_contact_id']],
                    'DueDate'     => $invoice['due_date'],
                    'InvoiceNumber' => $invoice['invoice_number'],
                    'LineItems'   => $lineItems,
                    'Status'      => 'AUTHORISED',
                ]],
            ]);

        return $response->successful()
            ? $response->json('Invoices.0.InvoiceID')
            : null;
    }

    private function getTenantId(Integration $integration): string
    {
        return $integration->settings['xero_tenant_id'] ?? '';
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
