<?php

namespace Modules\Accountings\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Orchestrates sync between this module and external accounting systems
 * (Xero, MYOB, QuickBooks) via the TitanIntegrations module when available.
 */
class AccountingsSyncService
{
    private const PROVIDER_XERO  = 'xero';
    private const PROVIDER_MYOB  = 'myob';
    private const PROVIDER_QB    = 'quickbooks';

    /** Account code mapping: cleaning service lines → Xero account codes */
    private const SERVICE_LINE_CODES = [
        'domestic_cleaning'    => '200',
        'commercial_cleaning'  => '201',
        'window_cleaning'      => '202',
        'carpet_cleaning'      => '203',
        'end_of_lease'         => '204',
        'pressure_washing'     => '205',
        'default'              => '200',
    ];

    // -------------------------------------------------------------------------
    // Xero
    // -------------------------------------------------------------------------

    public function syncInvoicesToXero(int $companyId): array
    {
        if (! $this->titanIntegrationsAvailable()) {
            return ['synced' => 0, 'skipped' => 0, 'errors' => ['TitanIntegrations module not available']];
        }

        $synced = 0;
        $skipped = 0;
        $errors = [];

        try {
            $schema = DB::getSchemaBuilder();
            if (! $schema->hasTable('invoices')) {
                return ['synced' => 0, 'skipped' => 0, 'errors' => ['invoices table not found']];
            }

            $exportedCol = $schema->hasColumn('invoices', 'exported_to_xero') ? 'exported_to_xero' : null;

            $query = DB::table('invoices')->where('company_id', $companyId);
            if ($exportedCol) {
                $query->where(fn($q) => $q->whereNull($exportedCol)->orWhere($exportedCol, false));
            }

            $invoices = $query->limit(100)->get();

            $integration = $this->getIntegration($companyId, self::PROVIDER_XERO);
            if (! $integration) {
                return ['synced' => 0, 'skipped' => (int) $invoices->count(), 'errors' => ['No active Xero integration']];
            }

            $xero = app(\Modules\TitanIntegrations\Services\Integrations\XeroIntegration::class);

            foreach ($invoices as $invoice) {
                try {
                    $xeroId = $xero->syncInvoice($integration, $this->mapInvoiceToXero($invoice));
                    if ($xeroId && $exportedCol) {
                        DB::table('invoices')->where('id', $invoice->id)->update([
                            'exported_to_xero'  => true,
                            'xero_invoice_id'   => $xeroId,
                        ]);
                    }
                    $synced++;
                } catch (\Throwable $e) {
                    $errors[] = "Invoice #{$invoice->id}: " . $e->getMessage();
                    $skipped++;
                }
            }
        } catch (\Throwable $e) {
            Log::error('[AccountingsSyncService] syncInvoicesToXero error', ['error' => $e->getMessage()]);
            $errors[] = $e->getMessage();
        }

        $this->updateSyncTimestamp($companyId, self::PROVIDER_XERO, 'invoices');

        return compact('synced', 'skipped', 'errors');
    }

    public function syncBillsToXero(int $companyId): array
    {
        if (! $this->titanIntegrationsAvailable()) {
            return ['synced' => 0, 'skipped' => 0, 'errors' => ['TitanIntegrations module not available']];
        }

        $synced = 0;
        $skipped = 0;
        $errors = [];

        try {
            $schema = DB::getSchemaBuilder();
            if (! $schema->hasTable('acc_bills')) {
                return ['synced' => 0, 'skipped' => 0, 'errors' => ['acc_bills table not found']];
            }

            $exportedCol = $schema->hasColumn('acc_bills', 'exported_to_xero') ? 'exported_to_xero' : null;

            $query = DB::table('acc_bills')->where('company_id', $companyId);
            if ($exportedCol) {
                $query->where(fn($q) => $q->whereNull($exportedCol)->orWhere($exportedCol, false));
            }

            $bills = $query->limit(100)->get();

            $integration = $this->getIntegration($companyId, self::PROVIDER_XERO);
            if (! $integration) {
                return ['synced' => 0, 'skipped' => (int) $bills->count(), 'errors' => ['No active Xero integration']];
            }

            $xero = app(\Modules\TitanIntegrations\Services\Integrations\XeroIntegration::class);

            foreach ($bills as $bill) {
                try {
                    $xeroId = $xero->syncInvoice($integration, $this->mapBillToXero($bill));
                    if ($xeroId && $exportedCol) {
                        DB::table('acc_bills')->where('id', $bill->id)->update([
                            'exported_to_xero' => true,
                            'xero_invoice_id'  => $xeroId,
                        ]);
                    }
                    $synced++;
                } catch (\Throwable $e) {
                    $errors[] = "Bill #{$bill->id}: " . $e->getMessage();
                    $skipped++;
                }
            }
        } catch (\Throwable $e) {
            Log::error('[AccountingsSyncService] syncBillsToXero error', ['error' => $e->getMessage()]);
            $errors[] = $e->getMessage();
        }

        $this->updateSyncTimestamp($companyId, self::PROVIDER_XERO, 'bills');

        return compact('synced', 'skipped', 'errors');
    }

    public function pullBillsFromXero(int $companyId): array
    {
        if (! $this->titanIntegrationsAvailable()) {
            return ['synced' => 0, 'skipped' => 0, 'errors' => ['TitanIntegrations module not available']];
        }

        // Pulling requires a GET /Invoices?Type=ACCPAY implementation in XeroIntegration
        // This is a stub — full implementation depends on XeroIntegration::getAccpayInvoices()
        Log::info('[AccountingsSyncService] pullBillsFromXero called', compact('companyId'));

        return ['synced' => 0, 'skipped' => 0, 'errors' => [], 'note' => 'Pull from Xero requires OAuth token refresh flow; use manual export from Xero.'];
    }

    // -------------------------------------------------------------------------
    // MYOB
    // -------------------------------------------------------------------------

    public function syncToMyob(int $companyId): array
    {
        if (! $this->titanIntegrationsAvailable()) {
            return ['synced' => 0, 'skipped' => 0, 'errors' => ['TitanIntegrations module not available']];
        }

        // Stub — MYOBIntegration class exists in TitanIntegrationsBase
        Log::info('[AccountingsSyncService] syncToMyob called (stub)', compact('companyId'));
        $this->updateSyncTimestamp($companyId, self::PROVIDER_MYOB, 'all');

        return ['synced' => 0, 'skipped' => 0, 'errors' => [], 'note' => 'MYOB sync not yet fully implemented.'];
    }

    // -------------------------------------------------------------------------
    // QuickBooks
    // -------------------------------------------------------------------------

    public function syncToQuickBooks(int $companyId): array
    {
        if (! $this->titanIntegrationsAvailable()) {
            return ['synced' => 0, 'skipped' => 0, 'errors' => ['TitanIntegrations module not available']];
        }

        Log::info('[AccountingsSyncService] syncToQuickBooks called (stub)', compact('companyId'));
        $this->updateSyncTimestamp($companyId, self::PROVIDER_QB, 'all');

        return ['synced' => 0, 'skipped' => 0, 'errors' => [], 'note' => 'QuickBooks sync not yet fully implemented.'];
    }

    // -------------------------------------------------------------------------
    // Status
    // -------------------------------------------------------------------------

    public function getSyncStatus(int $companyId): array
    {
        $providers = [self::PROVIDER_XERO, self::PROVIDER_MYOB, self::PROVIDER_QB];
        $status = [];

        foreach ($providers as $provider) {
            $integration = $this->getIntegration($companyId, $provider);
            $status[$provider] = [
                'connected'          => (bool) $integration,
                'last_synced_invoices' => $this->getLastSyncTimestamp($companyId, $provider, 'invoices'),
                'last_synced_bills'    => $this->getLastSyncTimestamp($companyId, $provider, 'bills'),
                'last_synced_all'      => $this->getLastSyncTimestamp($companyId, $provider, 'all'),
            ];
        }

        return $status;
    }

    // -------------------------------------------------------------------------
    // Account code mapping
    // -------------------------------------------------------------------------

    public function mapServiceLineToAccountCode(string $serviceLine): string
    {
        $key = strtolower(str_replace([' ', '-'], '_', $serviceLine));
        return self::SERVICE_LINE_CODES[$key] ?? self::SERVICE_LINE_CODES['default'];
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function titanIntegrationsAvailable(): bool
    {
        return class_exists(\Modules\TitanIntegrations\Services\Integrations\XeroIntegration::class);
    }

    private function getIntegration(int $companyId, string $provider): mixed
    {
        if (! DB::getSchemaBuilder()->hasTable('integrations')) {
            return null;
        }

        return DB::table('integrations')
            ->where('company_id', $companyId)
            ->where('provider', $provider)
            ->where('is_active', true)
            ->first();
    }

    private function mapInvoiceToXero(object $invoice): array
    {
        return [
            'type'             => 'ACCREC',
            'invoice_number'   => $invoice->invoice_number ?? "INV-{$invoice->id}",
            'due_date'         => $invoice->due_date ?? now()->addDays(14)->toDateString(),
            'xero_contact_id'  => $invoice->xero_contact_id ?? null,
            'items'            => [[
                'description' => "Invoice #{$invoice->id}",
                'quantity'    => 1,
                'unit_price'  => $invoice->sub_total ?? $invoice->total ?? 0,
            ]],
        ];
    }

    private function mapBillToXero(object $bill): array
    {
        return [
            'type'             => 'ACCPAY',
            'invoice_number'   => $bill->bill_number ?? "BILL-{$bill->id}",
            'due_date'         => $bill->due_date ?? now()->addDays(30)->toDateString(),
            'xero_contact_id'  => $bill->xero_contact_id ?? null,
            'items'            => [[
                'description' => "Bill #{$bill->id}: " . ($bill->description ?? ''),
                'quantity'    => 1,
                'unit_price'  => $bill->sub_total ?? $bill->total ?? 0,
            ]],
        ];
    }

    private function updateSyncTimestamp(int $companyId, string $provider, string $type): void
    {
        $key = "acc_sync_{$provider}_{$type}_{$companyId}";
        cache()->put($key, now()->toIso8601String(), now()->addDays(90));
    }

    private function getLastSyncTimestamp(int $companyId, string $provider, string $type): ?string
    {
        $key = "acc_sync_{$provider}_{$type}_{$companyId}";
        return cache()->get($key);
    }
}
