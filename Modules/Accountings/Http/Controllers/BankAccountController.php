<?php

namespace Modules\Accountings\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Modules\Accountings\Entities\BankAccount;
use Modules\Accountings\Services\AuditService;
use Modules\Accountings\Traits\ResolvesCompany;

class BankAccountController extends AccountBaseController
{
    use ResolvesCompany;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Bank Accounts';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accountings', $this->user->modules));
            return $next($request);
        });
    }

    public function index()
    {
        $accounts = BankAccount::orderBy('name')->get();
        return view('accountings::banking.accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('accountings::banking.accounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'institution' => 'nullable|string|max:191',
            'currency' => 'nullable|string|max:8',
            'account_number_last4' => 'nullable|string|max:8',
            'bsb' => 'nullable|string|max:16',
        ]);

        $acc = BankAccount::create([
            'name' => $request->name,
            'institution' => $request->institution,
            'currency' => $request->currency ?: 'AUD',
            'account_number_last4' => $request->account_number_last4,
            'bsb' => $request->bsb,
            'is_active' => 1,
        ]);

        AuditService::log('create', 'bank_account', $acc->id);

        return redirect()->route('bank-accounts.index')->with('success', 'Bank account created');
    }

    public function destroy($id)
    {
        $acc = BankAccount::where('company_id', $this->currentCompanyId())->findOrFail($id);
        $acc->delete();
        AuditService::log('delete', 'bank_account', (int)$id);
        return redirect()->route('bank-accounts.index')->with('success', 'Bank account removed');
    }
}