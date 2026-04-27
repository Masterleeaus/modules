<?php

namespace Modules\Accountings\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Modules\Accountings\Entities\BankAccount;
use Modules\Accountings\Entities\BankReconciliation;
use Modules\Accountings\Entities\BankReconciliationLine;
use Modules\Accountings\Entities\BankTransaction;
use Modules\Accountings\Services\AuditService;
use Modules\Accountings\Services\PeriodLockService;
use Modules\Accountings\Services\ReconciliationService;
use Modules\Accountings\Traits\ResolvesCompany;

class ReconciliationController extends AccountBaseController
{
    use ResolvesCompany;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Bank Reconciliation';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accountings', $this->user->modules));
            return $next($request);
        });
    }

    public function index()
    {
        $recs = BankReconciliation::latest('id')->paginate(20);
        $accounts = BankAccount::orderBy('name')->get();
        return view('accountings::banking.reconciliation.index', compact('recs','accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank_account_id' => 'required|integer',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'opening_balance' => 'required|numeric',
            'closing_balance' => 'required|numeric',
        ]);

        PeriodLockService::assertOpen($request->to_date);

        $rec = BankReconciliation::create([
            'bank_account_id' => (int)$request->bank_account_id,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'opening_balance' => (float)$request->opening_balance,
            'closing_balance' => (float)$request->closing_balance,
            'status' => 'draft',
        ]);

        ReconciliationService::recalc($rec);
        AuditService::log('create', 'reconciliation', $rec->id);

        return redirect()->route('banking.reconciliation.show', $rec->id);
    }

    public function show($id)
    {
        $rec = BankReconciliation::where('company_id', $this->currentCompanyId())->findOrFail($id);
        $account = $rec->bankAccount;

        $txnIds = BankReconciliationLine::where('reconciliation_id', $rec->id)->pluck('bank_transaction_id')->toArray();

        $available = BankTransaction::where('bank_account_id', $rec->bank_account_id)
            ->whereBetween('txn_date', [$rec->from_date, $rec->to_date])
            ->orderBy('txn_date')
            ->with('matches')
            ->get();

        $selected = BankTransaction::whereIn('id', $txnIds)->orderBy('txn_date')->with('matches')->get();

        $transactions = $available;

        return view('accountings::banking.reconciliation.show', compact('rec','account','available','selected','txnIds','transactions'));
    }

    public function addLine(Request $request, $id)
    {
        $rec = BankReconciliation::where('company_id', $this->currentCompanyId())->findOrFail($id);

        abort_if($rec->status === 'closed', 403, 'Reconciliation is closed');

        $request->validate(['bank_transaction_id' => 'required|integer']);
        $txnId = (int)$request->bank_transaction_id;

        BankReconciliationLine::firstOrCreate([
            'company_id' => $this->currentCompanyId(),
            'reconciliation_id' => $rec->id,
            'bank_transaction_id' => $txnId,
        ], [
            'added_at' => now(),
        ]);

        ReconciliationService::recalc($rec);
        AuditService::log('update', 'reconciliation', $rec->id, ['add_txn' => $txnId]);

        return redirect()->back()->with('success', 'Transaction added');
    }

    public function removeLine(Request $request, $id, $lineId)
    {
        $rec = BankReconciliation::where('company_id', $this->currentCompanyId())->findOrFail($id);

        abort_if($rec->status === 'closed', 403, 'Reconciliation is closed');

        $line = BankReconciliationLine::where('company_id', $this->currentCompanyId())->where('reconciliation_id', $rec->id)->where('id', $lineId)->firstOrFail();
        $txnId = $line->bank_transaction_id;
        $line->delete();

        ReconciliationService::recalc($rec);
        AuditService::log('update', 'reconciliation', $rec->id, ['remove_txn' => $txnId]);

        return redirect()->back()->with('success', 'Transaction removed');
    }

    public function close($id)
    {
        $rec = BankReconciliation::where('company_id', $this->currentCompanyId())->findOrFail($id);

        PeriodLockService::assertOpen($rec->to_date);

        abort_if($rec->status === 'closed', 403, 'Already closed');

        ReconciliationService::recalc($rec);

        $rec->status = 'closed';
        $rec->closed_at = now();
        $rec->save();

        AuditService::log('close', 'reconciliation', $rec->id);

        return redirect()->route('banking.reconciliation.index')->with('success', 'Reconciliation closed');
    }
}