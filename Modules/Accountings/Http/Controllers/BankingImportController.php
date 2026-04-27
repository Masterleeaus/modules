<?php

namespace Modules\Accountings\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Modules\Accountings\Entities\BankAccount;
use Modules\Accountings\Entities\BankTransaction;
use Modules\Accountings\Services\AuditService;
use Modules\Accountings\Services\BankImportService;

class BankingImportController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Banking Import';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accountings', $this->user->modules));
            return $next($request);
        });
    }

    public function index()
    {
        $accounts = BankAccount::orderBy('name')->get();
        $transactions = BankTransaction::latest('txn_date')->limit(200)->get();
        return view('accountings::banking.import.index', compact('accounts','transactions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank_account_id' => 'required|integer',
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $account = BankAccount::findOrFail((int)$request->bank_account_id);

        $path = $request->file('csv_file')->getRealPath();
        $result = BankImportService::importCsv($account->id, $path);

        AuditService::log('import', 'bank_txn', null, $result);

        return redirect()->route('banking.import.index')->with('success', 'Imported: '.$result['inserted'].' | Skipped: '.$result['skipped']);
    }
}
