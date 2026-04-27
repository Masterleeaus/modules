<?php

namespace Modules\Accountings\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Accountings\Services\AccountingsSyncService;

/**
 * Nightly job that syncs all companies with active accounting integrations.
 *
 * Dispatched via the scheduler (e.g. daily at 02:00).
 * Each company is processed independently so one failure does not
 * block the others.
 */
class NightlyAccountingsSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 300;

    public function handle(AccountingsSyncService $service): void
    {
        $companies = $this->resolveActiveCompanies();

        foreach ($companies as $companyId => $providers) {
            foreach ($providers as $provider) {
                try {
                    $result = match ($provider) {
                        'xero'       => array_merge(
                            $service->syncInvoicesToXero($companyId),
                            $service->syncBillsToXero($companyId)
                        ),
                        'myob'       => $service->syncToMyob($companyId),
                        'quickbooks' => $service->syncToQuickBooks($companyId),
                        default      => ['synced' => 0, 'skipped' => 0, 'errors' => []],
                    };

                    Log::info('[NightlyAccountingsSyncJob] sync complete', [
                        'company_id' => $companyId,
                        'provider'   => $provider,
                        'synced'     => $result['synced']  ?? 0,
                        'skipped'    => $result['skipped'] ?? 0,
                        'errors'     => count($result['errors'] ?? []),
                    ]);
                } catch (\Throwable $e) {
                    Log::error('[NightlyAccountingsSyncJob] sync failed', [
                        'company_id' => $companyId,
                        'provider'   => $provider,
                        'error'      => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('[NightlyAccountingsSyncJob] job failed', ['error' => $exception->getMessage()]);
    }

    /**
     * Returns [ company_id => ['xero'|'myob'|'quickbooks', ...], ... ]
     */
    private function resolveActiveCompanies(): array
    {
        $map = [];

        try {
            if (! DB::getSchemaBuilder()->hasTable('integrations')) {
                return $map;
            }

            $rows = DB::table('integrations')
                ->whereIn('provider', ['xero', 'myob', 'quickbooks'])
                ->where('is_active', true)
                ->select('company_id', 'provider')
                ->get();

            foreach ($rows as $row) {
                $map[$row->company_id][] = $row->provider;
            }
        } catch (\Throwable $e) {
            Log::warning('[NightlyAccountingsSyncJob] could not resolve companies', ['error' => $e->getMessage()]);
        }

        return $map;
    }
}
