<?php

namespace Modules\Accountings\Services;

use Illuminate\Support\Facades\Log;

/**
 * Xero sync service.
 *
 * Responsible for pushing an invoice to the Xero API.
 * This service is called exclusively from XeroSyncJob (via queue),
 * never synchronously within a request lifecycle.
 *
 * Double-export prevention: invoices that already have exported_to_xero = true
 * are skipped and a log notice is emitted.
 */
class XeroSyncService
{
    /**
     * Sync a single invoice to Xero.
     *
     * @param  int    $invoiceId  The core Invoice id
     * @param  int    $companyId  The owning company id
     * @return bool   true on successful sync, false if skipped or failed
     */
    public function syncInvoice(int $invoiceId, int $companyId): bool
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('invoices')) {
            Log::warning('[XeroSync] invoices table does not exist', ['invoice_id' => $invoiceId]);
            return false;
        }

        $invoice = \Illuminate\Support\Facades\DB::table('invoices')
            ->where('id', $invoiceId)
            ->where('company_id', $companyId)
            ->first();

        if (!$invoice) {
            Log::warning('[XeroSync] Invoice not found', ['invoice_id' => $invoiceId, 'company_id' => $companyId]);
            return false;
        }

        // Double-export prevention
        if (!empty($invoice->exported_to_xero)) {
            Log::notice('[XeroSync] Invoice already exported — skipping', [
                'invoice_id'     => $invoiceId,
                'xero_invoice_id' => $invoice->xero_invoice_id ?? null,
            ]);
            return false;
        }

        $credentials = $this->getCredentials($companyId);

        if (!$credentials) {
            Log::warning('[XeroSync] No Xero credentials configured', ['company_id' => $companyId]);
            return false;
        }

        try {
            $xeroInvoiceId = $this->postToXero($invoice, $credentials);

            \Illuminate\Support\Facades\DB::table('invoices')
                ->where('id', $invoiceId)
                ->update([
                    'exported_to_xero' => true,
                    'xero_invoice_id'  => $xeroInvoiceId,
                ]);

            Log::info('[XeroSync] Invoice synced successfully', [
                'invoice_id'      => $invoiceId,
                'xero_invoice_id' => $xeroInvoiceId,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('[XeroSync] Failed to sync invoice', [
                'invoice_id' => $invoiceId,
                'error'      => $e->getMessage(),
            ]);
            throw $e; // re-throw so the queue job can retry
        }
    }

    /**
     * Post invoice payload to the Xero API.
     *
     * @return string  The Xero-assigned invoice ID
     */
    protected function postToXero(object $invoice, array $credentials): string
    {
        // In a real implementation this would use the official Xero PHP SDK
        // or Guzzle to POST to https://api.xero.com/api.xro/2.0/Invoices
        // Returning a placeholder UUID here so the service is testable without live credentials.
        return (string) \Illuminate\Support\Str::uuid();
    }

    /**
     * Retrieve Xero API credentials for a company.
     * Returns null when not configured.
     */
    protected function getCredentials(int $companyId): ?array
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('acc_accounting_settings')) {
            return null;
        }

        $settings = \Illuminate\Support\Facades\DB::table('acc_accounting_settings')
            ->where('company_id', $companyId)
            ->first();

        if (!$settings) {
            return null;
        }

        $tenantId    = $settings->xero_tenant_id    ?? null;
        $clientId    = $settings->xero_client_id    ?? null;
        $clientSecret = $settings->xero_client_secret ?? null;

        if (!$tenantId || !$clientId || !$clientSecret) {
            return null;
        }

        return [
            'tenant_id'     => $tenantId,
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
        ];
    }
}
