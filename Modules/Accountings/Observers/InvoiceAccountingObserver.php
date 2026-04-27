<?php

namespace Modules\Accountings\Observers;

use Illuminate\Support\Facades\Log;
use Modules\Accountings\Jobs\XeroSyncJob;
use Modules\Accountings\Services\FinancialYearService;

/**
 * Observer for the core Invoice model.
 *
 * Responsibilities:
 *  - Auto-populate financial_year from the invoice issue_date (never manual)
 *  - Dispatch XeroSyncJob (queued) when invoice is saved and Xero columns exist
 */
class InvoiceAccountingObserver
{
    public function __construct(private FinancialYearService $fyService) {}

    /**
     * Handle the Invoice "creating" event.
     * Sets financial_year before the record is persisted.
     */
    public function creating(mixed $invoice): void
    {
        $this->setFinancialYear($invoice);
    }

    /**
     * Handle the Invoice "updating" event.
     * Recalculates financial_year if issue_date has changed.
     */
    public function updating(mixed $invoice): void
    {
        if ($invoice->isDirty('issue_date')) {
            $this->setFinancialYear($invoice);
        }
    }

    /**
     * Handle the Invoice "saved" event.
     * Dispatches XeroSyncJob when Xero columns are present and the invoice
     * has not already been exported.
     */
    public function saved(mixed $invoice): void
    {
        $this->dispatchXeroSync($invoice);
    }

    private function setFinancialYear(mixed $invoice): void
    {
        try {
            if (!isset($invoice->financial_year)) {
                return;
            }

            $dateValue = $invoice->issue_date ?? $invoice->created_at ?? now();
            $invoice->financial_year = $this->fyService->fromDate($dateValue, 'au');
        } catch (\Throwable $e) {
            Log::warning('[InvoiceAccountingObserver] Could not set financial_year', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function dispatchXeroSync(mixed $invoice): void
    {
        try {
            // Only dispatch if the exported_to_xero column exists on this model
            if (!isset($invoice->exported_to_xero)) {
                return;
            }

            // Do not dispatch if already exported (double-export prevention)
            if (!empty($invoice->exported_to_xero)) {
                return;
            }

            $companyId = $invoice->company_id ?? null;
            if (!$companyId || !$invoice->id) {
                return;
            }

            XeroSyncJob::dispatch((int) $invoice->id, (int) $companyId);
        } catch (\Throwable $e) {
            Log::warning('[InvoiceAccountingObserver] Could not dispatch XeroSyncJob', [
                'invoice_id' => $invoice->id ?? null,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
