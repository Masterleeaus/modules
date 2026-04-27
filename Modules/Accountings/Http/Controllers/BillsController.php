<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Support\Facades\DB;
use Modules\Accountings\Entities\Bill;
use Modules\Accountings\Entities\BillLine;
use Modules\Accountings\Entities\Vendor;
use Modules\Accountings\Entities\TaxCode;
use Modules\Accountings\Entities\ServiceLine;
use Modules\Accountings\Entities\Accounting;
use Modules\Accountings\Entities\JobCost;
use Modules\Accountings\Entities\BillPayment;
use Modules\Accountings\Services\PeriodLockService;
use Modules\Accountings\Traits\ResolvesCompany;

/**
 * Pass 2: Bills (Accounts Payable) workflow for cleaning businesses.
 *
 * This is deliberately "thin UI": it enables entering bills, lines and
 * optional job_ref allocations for profitability tracking.
 */
class BillsController extends AccountBaseController
{
    use ResolvesCompany;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Bills';
        $this->pageIcon = 'ti-receipt';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accountings', $this->user->modules));
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $status = $request->get('status');
        $query = Bill::query()->with('vendor')->orderByDesc('id');
        if ($status) {
            $query->where('status', $status);
        }

        $bills = $query->paginate(25);
        return view('accountings::bills.index', compact('bills', 'status'));
    }

    public function create()
    {
        $vendors = Vendor::query()->orderBy('name')->get();
        $taxCodes = TaxCode::query()->orderBy('code')->get();
        $serviceLines = ServiceLine::query()->orderBy('name')->get();
        $accounts = Accounting::query()->orderBy('coa')->get();

        return view('accountings::bills.create', compact('vendors', 'taxCodes', 'serviceLines', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'nullable|integer',
            'bill_number' => 'nullable|string|max:191',
            'bill_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'status' => 'nullable|string|max:30',
            'notes' => 'nullable|string',

            'lines' => 'array',
            'lines.*.description' => 'nullable|string|max:191',
            'lines.*.coa_id' => 'nullable|integer',
            'lines.*.tax_code_id' => 'nullable|integer',
            'lines.*.service_line_id' => 'nullable|integer',
            'lines.*.qty' => 'nullable|numeric',
            'lines.*.unit_price' => 'nullable|numeric',
            'lines.*.job_ref' => 'nullable|string|max:191',
        ]);

        PeriodLockService::assertOpen($request->get('bill_date'));

        $status = $validated['status'] ?? 'draft';

        DB::transaction(function () use ($validated, $status) {
            $bill = new Bill();
            $bill->vendor_id = $validated['vendor_id'] ?? null;
            $bill->bill_number = $validated['bill_number'] ?? null;
            $bill->bill_date = $validated['bill_date'] ?? null;
            $bill->due_date = $validated['due_date'] ?? null;
            $bill->status = $status;
            $bill->notes = $validated['notes'] ?? null;
            $bill->subtotal = 0;
            $bill->tax_total = 0;
            $bill->total = 0;
            $bill->save();

            $subtotal = 0;
            $taxTotal = 0;

            foreach (($validated['lines'] ?? []) as $line) {
                $qty = (float)($line['qty'] ?? 1);
                $unit = (float)($line['unit_price'] ?? 0);
                $lineSubtotal = round($qty * $unit, 2);

                $taxCode = null;
                if (!empty($line['tax_code_id'])) {
                    $taxCode = TaxCode::find($line['tax_code_id']);
                }
                $rate = $taxCode ? (float)$taxCode->rate : 0;
                $lineTax = round($lineSubtotal * $rate, 2);
                $lineTotal = round($lineSubtotal + $lineTax, 2);

                $billLine = new BillLine();
                $billLine->bill_id = $bill->id;
                $billLine->coa_id = $line['coa_id'] ?? null;
                $billLine->tax_code_id = $line['tax_code_id'] ?? null;
                $billLine->service_line_id = $line['service_line_id'] ?? null;
                $billLine->description = $line['description'] ?? null;
                $billLine->qty = $qty;
                $billLine->unit_price = $unit;
                $billLine->line_subtotal = $lineSubtotal;
                $billLine->line_tax = $lineTax;
                $billLine->line_total = $lineTotal;
                $billLine->save();

                $subtotal += $lineSubtotal;
                $taxTotal += $lineTax;

                // Optional profitability allocation
                $jobRef = trim((string)($line['job_ref'] ?? ''));
                if ($jobRef !== '') {
                    $jc = new JobCost();
                    $jc->job_ref = $jobRef;
                    $jc->source_bill_line_id = $billLine->id;
                    $jc->service_line_id = $billLine->service_line_id;
                    $jc->amount = $billLine->line_total;
                    $jc->cost_type = 'expense';
                    $jc->save();
                }
            }

            $bill->subtotal = round($subtotal, 2);
            $bill->tax_total = round($taxTotal, 2);
            $bill->total = round($subtotal + $taxTotal, 2);
            $bill->save();
        });

        return redirect()->route('bills.index')->with('message', 'Bill created');
    }

    public function show($id)
    {
        $bill = Bill::query()->with(['vendor', 'lines', 'lines.taxCode', 'lines.serviceLine', 'payments'])->findOrFail($id);
        return view('accountings::bills.show', compact('bill'));
    }


    public function storePayment(Request $request, $id)
    {
        $bill = Bill::where('company_id', $this->currentCompanyId())->findOrFail($id);

        $validated = $request->validate([
            'paid_at' => 'nullable|date',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:191',
            'notes' => 'nullable|string',
        ]);

        PeriodLockService::assertOpen($request->get('paid_at'));

        PeriodLockService::assertOpen($request->get('bill_date'));

        DB::transaction(function () use ($bill, $validated) {
            $p = new BillPayment();
            $p->bill_id = $bill->id;
            $p->paid_at = $validated['paid_at'] ?? now()->toDateString();
            $p->amount = round((float)$validated['amount'], 2);
            $p->method = $validated['method'] ?? null;
            $p->reference = $validated['reference'] ?? null;
            $p->notes = $validated['notes'] ?? null;
            $p->save();

            // Update bill status based on balance due
            $paidTotal = (float) BillPayment::where('bill_id', $bill->id)->sum('amount');
            if ($paidTotal >= (float)$bill->total) {
                $bill->status = 'paid';
            } elseif ($paidTotal > 0) {
                $bill->status = 'partial';
            } else {
                $bill->status = $bill->status ?: 'unpaid';
            }
            $bill->save();
        });

        return redirect()->route('bills.show', $bill->id)->with('message', 'Payment recorded');
    }

    public function destroy($id)
    {
        $bill = Bill::where('company_id', $this->currentCompanyId())->findOrFail($id);
        DB::transaction(function () use ($bill) {
            BillLine::where('company_id', $this->currentCompanyId())->where('bill_id', $bill->id)->delete();
            $bill->delete();
        });

        return redirect()->route('bills.index')->with('message', 'Bill deleted');
    }
}