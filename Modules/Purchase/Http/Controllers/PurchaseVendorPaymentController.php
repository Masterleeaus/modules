<?php

namespace Modules\Purchase\Http\Controllers;

use Carbon\Carbon;
use App\Helper\Reply;
use App\Models\Invoice;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Modules\Purchase\Entities\PurchaseBill;
use Illuminate\Contracts\Support\Renderable;
use Modules\Purchase\Entities\PurchaseOrder;
use Modules\Purchase\Entities\PurchaseVendor;
use Modules\Purchase\Entities\PurchaseSetting;
use App\Http\Controllers\AccountBaseController;
use App\Models\Expense;
use App\Models\ExpensesCategory;
use Modules\Purchase\Entities\PurchasePaymentBill;
use Modules\Purchase\Entities\PurchaseVendorCredit;
use Modules\Purchase\Entities\PurchaseVendorPayment;
use Modules\Purchase\Entities\PurchasePaymentHistory;
use Modules\Purchase\Entities\PurchaseStockAdjustment;
use Modules\Purchase\DataTables\VendorPaymentDataTable;
use Modules\Purchase\Http\Requests\VendorPayment\StoreRequest;

class PurchaseVendorPaymentController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('purchase::app.purchaseOrder.vendorPayments');
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function index(VendorPaymentDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_vendor_payment');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));
        $this->pageTitle = __('purchase::app.purchaseOrder.vendorPayments');

        return $dataTable->render('purchase::vendor-payments.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $addPermission = user()->permission('add_vendor_payment');
        abort_403(!in_array($addPermission, ['all', 'added']));
        $this->pageTitle = __('purchase::modules.vendorPayment.addPayment');
        $this->vendors = PurchaseVendor::all();
        $this->bills = PurchaseBill::all();
        $this->vendorID = request()->payment_vendor_id;

        if (request('bill') != '') {
            $billId = request('bill');
            $this->type = 'bill';
            $this->purchaseBill = PurchaseBill::with('vendor', 'order')
                ->whereIn('status', ['open', 'partially_paid'])
                ->findOrFail($billId);
        }

        if (request()->ajax()) {
            $html = view('purchase::vendor-payments.ajax.create', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'purchase::vendor-payments.ajax.create';

        return view('purchase::vendor-payments.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(StoreRequest $request)
    {
        $redirectUrl = urldecode($request->redirect_url);

        if ($redirectUrl == '') {
            $redirectUrl = route('vendor-payments.index');
        }

        $payment = new PurchaseVendorPayment();
        $payment->purchase_vendor_id = $request->vendor_id;
        $payment->payment_date = $request->payment_date == null ? $request->payment_date : Carbon::createFromFormat($this->company->date_format, $request->payment_date)->format('Y-m-d');
        $payment->bank_account_id = $request->bank_account_id;
        $payment->received_payment = round($request->payment_made, 2);
        $payment->excess_payment = round($request->excess, 2);
        $payment->internal_note = $request->internal_note;
        $payment->notify_vendor = $request->notify_vendor ?? 0;
        $payment->save();
        $amounts = array_combine($request->bill_id, $request->amount_paid_per);

        $this->vendors = PurchaseVendor::where('id',$request->vendor_id)->first();
        $billNumber =PurchaseBill::where('purchase_vendor_id',$request->vendor_id);
        $countBill = count($request->bill_id);
        if ($countBill > 1) {
            $billNumber = $billNumber->pluck('purchase_bill_number')->toArray();
        }
        else{
            $billNumber = $billNumber->whereIn('id',$request->bill_id)->pluck('purchase_bill_number')->toArray();
        }
        $prefix =PurchaseSetting::first();
        $prefixBill = array_map(fn($item) => $prefix->bill_prefix .'#'. $item, $billNumber);
        $prefixBill = implode(",", $prefixBill);
        $expenses =new Expense();
        $expenses->item_name = __('purchase::app.purchaseOrder.paymentToVendor');
        $expenses->purchase_date = $request->payment_date == null ? $request->payment_date : Carbon::createFromFormat($this->company->date_format, $request->payment_date)->format('Y-m-d');
        $expenses->price = round($request->payment_made, 2);
        $expenses->user_id = user()->id;
        $expenses->company_id = company()->id;
        $expenses->description = $prefixBill. '  ' . '  ,'. 'Excess Payment'. ' = ' .round($request->excess, 2);
        $expenses->currency_id = $this->vendors->currency_id;
        $expenses->purchase_from = $this->vendors->primary_name;
        $expenses->save();

        foreach ($amounts as $key => $amt) {
            $bill = PurchaseBill::with('order', 'order.items')->where('id', $key)->first();

            $totalPaid = PurchasePaymentBill::where('purchase_bill_id', $bill->id)->sum('total_paid');
            $item = new PurchasePaymentBill();
            $item->purchase_bill_id = $key;
            $item->total_paid = $amt;
            $item->purchase_vendor_id = $request->vendor_id;
            $item->purchase_vendor_payment_id = $payment->id;
            $item->save();

            if ((float)($totalPaid + $amt) >= (float)$bill->total) {
                $bill->status = 'paid';
            }
            elseif ($totalPaid + $amt == 0) {
                $bill->status = 'open';
            }
            else {
                $bill->status = 'partially_paid';
            }

            $bill->save();
        }

        if ($payment->excess_payment > 0) {
            $vendorCredit = new PurchaseVendorCredit();

            if (!is_null($vendorCredit->latest()->first())) {
                $CreditNoteNo = $vendorCredit->latest()->first()->credit_note_no + 1;
            }
            else {
                $CreditNoteNo = 1;
            }

            $vendorCredit->payment_id = $payment->id;
            $vendorCredit->vendor_id = $payment->purchase_vendor_id;
            $vendorCredit->credit_note_no = $CreditNoteNo;
            $vendorCredit->credit_date = $payment->payment_date;
            $vendorCredit->total = $payment->excess_payment;
            $vendorCredit->currency_id = $payment->vendor->currency_id;
            $vendorCredit->save();
        }

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => $redirectUrl]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $viewPermission = user()->permission('view_vendor_payment');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned']));
        $this->vendorPayment = PurchaseVendorPayment::with('vendor', 'bankAccount', 'vendor.currency')->findOrFail($id);
        $this->bills = PurchasePaymentBill::with('bill', 'bill.order')->where('purchase_vendor_id', $this->vendorPayment->purchase_vendor_id)->where('purchase_vendor_payment_id', $this->vendorPayment->id)->get();

        $this->view = 'purchase::vendor-payments.ajax.overview';
        $tab = request('tab');
        $billArray = [];

        foreach ($this->bills as $bill) {
            $amt = PurchasePaymentBill::where('purchase_bill_id', $bill->bill->id)->sum('total_paid');
            $billArray[$bill->id] = $amt;
        }

        $this->billArray = $billArray;

        switch ($tab) {
        case 'history':
            $this->history = PurchasePaymentHistory::where('purchase_payment_id', $id)->orderByDesc('id')->get();
            $this->view = 'purchase::vendor-payments.ajax.history';
            break;
        default:
            $this->view = 'purchase::vendor-payments.ajax.overview';
            break;
        }

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->activeTab = $tab ?: 'overview';

        return view('purchase::vendor-payments.show', $this->data);

    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->payment = PurchaseVendorPayment::with('vendor', 'vendor.currency')->findOrFail($id);
        $this->editPermission = user()->permission('edit_vendor_payment');
        abort_403(!($this->editPermission == 'all'
            || ($this->editPermission == 'added' && $this->payment->added_by == user()->id)));

        $this->vendors = PurchaseVendor::all();
        $this->bills = PurchasePaymentBill::with('bill', 'bill.order')->where('purchase_vendor_payment_id', $id)->get();

        $billArray = [];

        foreach ($this->bills as $bill) {
            $amt = PurchasePaymentBill::where('purchase_bill_id', $bill->bill->id)->sum('total_paid');
            $billArray[$bill->id] = $amt;
        }

        $this->billArray = $billArray;
        $amountUsed = $this->bills->pluck('total_paid')->toArray();
        $this->amountUsedSum = array_sum($amountUsed);
        $this->allBills = PurchaseBill::where('purchase_vendor_id', $this->payment->purchase_vendor_id)->pluck('id');

        $this->viewBankAccountPermission = user()->permission('view_bankaccount');
        $bankAccounts = BankAccount::where('status', 1)->where('currency_id', $this->payment->vendor->currency_id);

        if ($this->viewBankAccountPermission == 'added') {
            $bankAccounts = $bankAccounts->where('added_by', user()->id);
        }

        $bankAccounts = $bankAccounts->get();
        $this->bankDetails = $bankAccounts;

        if (request()->ajax()) {
            $html = view('purchase::vendor-payments.ajax.edit', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'purchase::vendor-payments.ajax.edit';

        return view('purchase::vendor-payments.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(StoreRequest $request, $id)
    {
        $payment = PurchaseVendorPayment::findOrFail($id);
        $payment->purchase_vendor_id = $request->purchase_vendor_id;
        $payment_date = Carbon::createFromFormat($this->company->date_format, $request->payment_date)->format('Y-m-d');
        $payment->payment_date = $payment_date;
        $payment->bank_account_id = $request->bank_account_id;
        $payment->internal_note = $request->internal_note;
        $payment->save();


        $amounts = array_combine($request->bill_id, $request->amount_paid_per);

        if(!($amounts === $amounts)){
            PurchasePaymentBill::where('purchase_vendor_payment_id', $id)->delete();

            foreach ($amounts as $key => $amt) {
                $bill = PurchaseBill::with('order')->where('id', $key)->first();

                $item = new PurchasePaymentBill();
                $item->purchase_bill_id = $key;
                $item->total_paid = $amt;
                $item->purchase_vendor_id = $request->vendor_id;
                $item->purchase_vendor_payment_id = $payment->id;
                $item->save();
                $totalPaid = PurchasePaymentBill::where('purchase_bill_id', $bill->id)->sum('total_paid');

                if ($amt < $bill->total) {
                    $bill->status = 'partially_paid';
                    $bill->save();
                }

                if ($amt == $bill->total || $totalPaid == $bill->total) {
                    $bill->status = 'paid';
                    $bill->save();
                }
            }
        }

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('vendor-payments.index')]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $vendorPayment = PurchaseVendorPayment::findOrFail($id);

        $vendorBillsAmt = array();
        PurchasePaymentBill::where('purchase_vendor_payment_id', $vendorPayment->id)->get()->map(function ($item) use (&$vendorBillsAmt) {
            $vendorBillsAmt[$item->purchase_bill_id] = $item->total_paid;
        });

        foreach ($vendorBillsAmt as $key => $item) {
            $revertBill = PurchaseBill::where('id', $key)->first();
            $totalPaid = PurchasePaymentBill::where('purchase_bill_id', $key)->sum('total_paid');

            if ($item == $revertBill->total || ($totalPaid) - ($revertBill->total) < 0) {
                $revertBill->status = 'open';
                $revertBill->save();
            }

            if ($item < $totalPaid) {
                $revertBill->status = 'partially_paid';
                $revertBill->save();
            }
        }

        $this->deletePermission = user()->permission('delete_vendor_payment');
        abort_403(!($this->deletePermission == 'all'
            || ($this->deletePermission == 'added' && $vendorPayment->added_by == user()->id)));
        $vendorPayment->delete();

        return Reply::successWithData(__('messages.deleteSuccess'), ['redirectUrl' => route('vendor-payments.index')]);
    }

    public function applyQuickAction(Request $request)
    {
        switch ($request->action_type) {
        case 'delete':
            $this->deleteRecords($request);

            return Reply::success(__('messages.deleteSuccess'));
        default:
            return Reply::error(__('messages.selectAction'));
        }
    }

    protected function deleteRecords($request)
    {
        abort_403(user()->permission('delete_vendor_payment') != 'all');

        $purchaseVendorPayment = PurchaseVendorPayment::whereIn('id', explode(',', $request->row_ids))->get();

        foreach ($purchaseVendorPayment as $paymentData){
            $paymentData->delete();
        }

        return true;
    }

    public function fetchBills($id, Request $request)
    {

        if ($request->bill) {
            $this->bills = PurchaseBill::with('vendor', 'order')
                ->where('purchase_vendor_id', $id)
                ->where('id', $request->bill)
                ->whereIn('status', ['open', 'partially_paid'])
                ->get();

        }
        else {
            $this->bills = PurchaseBill::with('vendor', 'order')
                ->where('purchase_vendor_id', $id)
                ->whereIn('status', ['open', 'partially_paid'])
                ->get();
        }

        $billArray = [];

        foreach ($this->bills as $bill) {
            if ($bill->status == 'partially_paid') {
                $amt = PurchasePaymentBill::where('purchase_bill_id', $bill->id)->sum('total_paid');
                $billArray[$bill->id] = $amt;
            }
        }
        if ($id != 'all') {
            $this->billArray = $billArray;
            $this->vendor = PurchaseVendor::with('currency')->where('id', $id)->first();
            $this->allBills = $this->bills->pluck('id');
            $this->viewBankAccountPermission = user()->permission('view_bankaccount');
            $bankAccounts = BankAccount::where('status', 1)->where('currency_id', $this->vendor->currency_id);

            if ($this->viewBankAccountPermission == 'added') {
                $bankAccounts = $bankAccounts->where('added_by', user()->id);
            }

            $bankAccounts = $bankAccounts->get();
            $this->bankDetails = $bankAccounts;
        }
        else {
            $this->allBills = $this->bills->pluck('id');
        }

        $view = view('purchase::vendor-payments.ajax.fetch-bills', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'data' => $this->data, 'html' => $view]);
    }

    public function download($id)
    {
        $this->vendorPayment = PurchaseVendorPayment::with('vendor', 'bankAccount', 'vendor.currency', 'user')->findOrFail($id);
        $this->bills = PurchasePaymentBill::with('bill', 'bill.order')->where('purchase_vendor_id', $this->vendorPayment->purchase_vendor_id)->where('purchase_vendor_payment_id', $this->vendorPayment->id)->get();
        if ($this->bills->isEmpty()) {
            abort(404);

        }

        $billArray = [];

        foreach ($this->bills as $bill) {

            $amt = PurchasePaymentBill::where('purchase_bill_id', $bill->bill->id)->sum('total_paid');
            $billArray[$bill->id] = $amt;
        }
        $this->billArray = $billArray;
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('purchase::vendor-payments.pdf.payment', $this->data);
        $filename = __('app.receipt');
        return $pdf->download($filename . '.pdf');

    }

}
