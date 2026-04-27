<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Entities\BankTransaction;
use Modules\Accountings\Entities\PeriodLock;
use Modules\Accountings\Services\AuditService;
use Modules\Accountings\Services\BankImportService;

class BankImportController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Banking Import';
        $this->pageIcon  = 'ti-upload';

        $this->middleware(function ($request, $next) {
            abort_403(! in_array('accountings', $this->user->modules));
            return $next($request);
        });
    }

    public function index()
    {
        $transactions = BankTransaction::where('company_id', $this->user->company_id)
            ->orderByDesc('txn_date')
            ->limit(200)
            ->get();

        return view('accountings::banking.import', compact('transactions'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'csv_file'        => 'required|file|mimes:csv,txt',
            'bank_account_id' => 'required|integer',
        ]);

        $companyId     = $this->user->company_id;
        $userId        = $this->user->id;
        $bankAccountId = (int) $request->bank_account_id;

        $result = BankImportService::importCsv($bankAccountId, $request->file('csv_file')->getRealPath());

        AuditService::log('bank_import', 'bank_transaction', null, $result);

        return redirect()->route('accountings.banking.import')
            ->with('success', "Imported: {$result['inserted']} | Skipped: {$result['skipped']}");
    }

    public function match(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|integer',
            'matched_type'   => 'required|in:bill,expense,invoice',
            'matched_id'     => 'required|integer',
        ]);

        $tx = BankTransaction::where('company_id', $this->user->company_id)
            ->findOrFail($request->transaction_id);

        $tx->update([
            'matched_type' => $request->matched_type,
            'matched_id'   => $request->matched_id,
            'matched_at'   => now(),
        ]);

        AuditService::log('bank_match', 'bank_transaction', $tx->id, $request->only('matched_type', 'matched_id'));

        return redirect()->route('accountings.banking.import')->with('success', 'Transaction matched.');
    }
}
