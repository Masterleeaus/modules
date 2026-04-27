<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Services\AccountingsSyncService;

class AccountingsSyncController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Accounting Sync';
        $this->pageIcon  = 'ti-refresh';

        $this->middleware(function ($request, $next) {
            abort_403(! in_array('accountings', $this->user->modules));
            return $next($request);
        });
    }

    public function index()
    {
        $service   = new AccountingsSyncService();
        $companyId = $this->user->company_id;
        $status    = $service->getSyncStatus($companyId);

        return view('accountings::settings.sync', compact('status'));
    }

    public function sync(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:xero,myob,quickbooks',
        ]);

        $service   = new AccountingsSyncService();
        $companyId = $this->user->company_id;
        $provider  = $request->provider;

        $result = match ($provider) {
            'xero'        => array_merge(
                $service->syncInvoicesToXero($companyId),
                $service->syncBillsToXero($companyId)
            ),
            'myob'        => $service->syncToMyob($companyId),
            'quickbooks'  => $service->syncToQuickBooks($companyId),
        };

        $synced  = $result['synced']  ?? 0;
        $skipped = $result['skipped'] ?? 0;
        $errors  = count($result['errors'] ?? []);

        return redirect()->route('accountings.settings.sync')
            ->with('success', ucfirst($provider) . " sync complete. Synced: {$synced}, Skipped: {$skipped}, Errors: {$errors}");
    }
}
