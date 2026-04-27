<?php

namespace Modules\Accountings\Http\Controllers;

use App\Helper\Reply;
use Modules\Accountings\Entities\PNL;
use Modules\Accountings\Entities\BalanceSheet;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Entities\JournalType;

class AccSettingController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle         = 'accountings::modules.acc.accountingSettings';
        $this->activeSettingMenu = 'accountings_settings';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accountings', $this->user->modules));
            return $next($request);
        });
    }

      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->pnl         = PNL::all();
        $this->bs          = BalanceSheet::all();
        $this->journaltype = JournalType::all();
        $this->view        = 'accountings::accounting-settings.ajax.bs';

        $tab = request('tab');
        switch ($tab) {
            case 'pnl': 
                $this->view = 'accountings::accounting-settings.ajax.pnl';
                break;
            case 'type': 
                $this->view = 'accountings::accounting-settings.ajax.type';
                break;
            default: 
                $this->view = 'accountings::accounting-settings.ajax.bs';
                break;
        }

        $this->activeTab = $tab ?: 'typeunit';
        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle, 'activeTab' => $this->activeTab]);
        }

        return view('accountings::accounting-settings.index', $this->data);
    }

public function cashflowSetup()
{
    $budget = \Modules\Accountings\Entities\CashflowBudget::where('is_active',1)->latest('id')->first();
    $recurrings = \Modules\Accountings\Entities\RecurringExpense::orderBy('name')->get();

    return view('accountings::settings.cashflow', compact('budget','recurrings'));
}

public function saveCashflowSetup(\Illuminate\Http\Request $request)
{
    $request->validate([
        'budget_name' => 'nullable|string|max:191',
        'expected_monthly_inflow' => 'nullable|numeric',
        'expected_monthly_outflow' => 'nullable|numeric',
    ]);

    // Upsert active budget
    $name = $request->get('budget_name');
    if ($name) {
        \Modules\Accountings\Entities\CashflowBudget::query()->update(['is_active' => 0]);
        \Modules\Accountings\Entities\CashflowBudget::create([
            'name' => $name,
            'expected_monthly_inflow' => (float)$request->get('expected_monthly_inflow', 0),
            'expected_monthly_outflow' => (float)$request->get('expected_monthly_outflow', 0),
            'is_active' => 1,
        ]);
    }

    return redirect()->back()->with('message', 'Cashflow settings saved.');
}

public function addRecurringExpense(\Illuminate\Http\Request $request)
{
    $request->validate([
        'name' => 'required|string|max:191',
        'amount' => 'required|numeric',
        'frequency' => 'required|in:weekly,monthly,quarterly,yearly',
    ]);

    \Modules\Accountings\Entities\RecurringExpense::create([
        'name' => $request->name,
        'amount' => (float)$request->amount,
        'frequency' => $request->frequency,
        'is_active' => 1,
    ]);

    return redirect()->back()->with('message', 'Recurring expense added.');
}

public function toggleRecurringExpense($id)
{
    $r = \Modules\Accountings\Entities\RecurringExpense::findOrFail($id);
    $r->is_active = !$r->is_active;
    $r->save();

    return redirect()->back()->with('message', 'Recurring expense updated.');
}

}
