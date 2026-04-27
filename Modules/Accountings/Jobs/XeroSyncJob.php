<?php

namespace Modules\Accountings\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Accountings\Services\XeroSyncService;

/**
 * Queue job that syncs a core Invoice record to Xero.
 *
 * Retried up to 3 times with exponential back-off.
 * Sync happens asynchronously — never in the HTTP request lifecycle.
 */
class XeroSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Max attempts before the job is marked as failed */
    public int $tries = 3;

    /** Back-off in seconds between retries (10s, 60s, 300s) */
    public array $backoff = [10, 60, 300];

    public function __construct(
        public readonly int $invoiceId,
        public readonly int $companyId
    ) {}

    public function handle(XeroSyncService $service): void
    {
        $service->syncInvoice($this->invoiceId, $this->companyId);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('[XeroSyncJob] Job ultimately failed after all retries', [
            'invoice_id' => $this->invoiceId,
            'company_id' => $this->companyId,
            'error'      => $exception->getMessage(),
        ]);
    }
}
