<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Support\Facades\DB;
use Modules\Accountings\Entities\Expense;
use Modules\Accountings\Entities\Vendor;
use Modules\Accountings\Entities\TaxCode;
use Modules\Accountings\Entities\ServiceLine;
use Modules\Accountings\Entities\Accounting;
use Modules\Accountings\Entities\JobCost;
use Modules\Accountings\Services\PeriodLockService;
use Modules\Accountings\Traits\ResolvesCompany;

class ExpensesController extends AccountBaseController
{
    use ResolvesCompany;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Expenses';
        $this->pageIcon = 'ti-money';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accountings', $this->user->modules));
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = Expense::query()->with('vendor')->orderByDesc('expense_date')->orderByDesc('id');

        if ($request->filled('from')) {
            $query->whereDate('expense_date', '>=', $request->get('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('expense_date', '<=', $request->get('to'));
        }

        $expenses = $query->paginate(25);
        return view('accountings::expenses.index', compact('expenses'));
    }

    public function create()
    {
        $vendors = Vendor::query()->orderBy('name')->get();
        $taxCodes = TaxCode::query()->orderBy('code')->get();
        $serviceLines = ServiceLine::query()->orderBy('name')->get();
        $accounts = Accounting::query()->orderBy('coa')->get();

        return view('accountings::expenses.create', compact('vendors', 'taxCodes', 'serviceLines', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_date' => 'nullable|date',
            'vendor_id' => 'nullable|integer',
            'coa_id' => 'nullable|integer',
            'tax_code_id' => 'nullable|integer',
            'service_line_id' => 'nullable|integer',
            'payment_method' => 'nullable|string|max:30',
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:191',
            'job_ref' => 'nullable|string|max:191',
            'notes' => 'nullable|string',
        ]);

        PeriodLockService::assertOpen($request->get('expense_date'));

        DB::transaction(function () use ($validated) {
            $taxCode = null;
            if (!empty($validated['tax_code_id'])) {
                $taxCode = TaxCode::find($validated['tax_code_id']);
            }

            $amount = round((float)$validated['amount'], 2);
            $rate = $taxCode ? (float)$taxCode->rate : 0;
            $taxAmount = round($amount * $rate / (1 + $rate), 2); // assumes amount includes GST when rate > 0

            $exp = new Expense();
            $exp->expense_date = $validated['expense_date'] ?? now()->toDateString();
            $exp->vendor_id = $validated['vendor_id'] ?? null;
            $exp->coa_id = $validated['coa_id'] ?? null;
            $exp->tax_code_id = $validated['tax_code_id'] ?? null;
            $exp->service_line_id = $validated['service_line_id'] ?? null;
            $exp->payment_method = $validated['payment_method'] ?? 'cash';
            $exp->amount = $amount;
            $exp->tax_amount = $taxAmount;
            $exp->description = $validated['description'] ?? null;
            $exp->job_ref = $validated['job_ref'] ?? null;
            $exp->notes = $validated['notes'] ?? null;
            $exp->status = 'posted';
            $exp->save();

            $jobRef = trim((string)($validated['job_ref'] ?? ''));
            if ($jobRef !== '') {
                $jc = new JobCost();
                $jc->job_ref = $jobRef;
                $jc->service_line_id = $exp->service_line_id;
                $jc->amount = $amount;
                $jc->cost_type = 'expense';
                $jc->save();
            }
        });

        return redirect()->route('expenses.index')->with('message', 'Expense saved');
    }

    public function show($id)
    {
        $expense = Expense::query()->with(['vendor', 'taxCode', 'serviceLine'])->findOrFail($id);
        return view('accountings::expenses.show', compact('expense'));
    }

    public function destroy($id)
    {
        $expense = Expense::where('company_id', $this->currentCompanyId())->findOrFail($id);
        $expense->delete();
        return redirect()->route('expenses.index')->with('message', 'Expense deleted');
    }
}