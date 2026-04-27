<?php

namespace Modules\Purchase\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tax;
use App\Helper\Files;
use App\Helper\Reply;
use App\Models\Product;
use App\Models\Currency;
use App\Models\UnitType;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\App;
use Modules\Purchase\Entities\PurchaseBill;
use Modules\Purchase\Entities\PurchaseItem;
use Illuminate\Contracts\Support\Renderable;
use Modules\Purchase\Entities\PurchaseOrder;
use Modules\Purchase\Entities\PurchaseVendor;
use Modules\Purchase\Entities\PurchaseProduct;
use Modules\Purchase\Entities\PurchaseSetting;
use App\Http\Controllers\AccountBaseController;
use Modules\Purchase\Entities\PurchaseVendorItem;
use Modules\Purchase\Entities\PurchasePaymentBill;
use Modules\Purchase\Entities\PurchaseVendorCredit;
use Modules\Purchase\Events\VendorCreditPaymentMade;
use Modules\Purchase\DataTables\VendorCreditDataTable;
use Modules\Purchase\Entities\PurchaseVendorCreditHistory;
use Modules\Purchase\Entities\PurchaseVendorCreditItemImage;
use Modules\Purchase\Http\Requests\VendorCredits\UpdateVendorCreditRequest;
class VendorCreditController extends AccountBaseController
{

    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('purchase::app.menu.addVendorCredit');

        $this->middleware(function ($request, $next) {
            abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    public function index(VendorCreditDataTable $dataTable)
    {
        $this->pageTitle = __('purchase::app.menu.vendorCredits');
        $this->vendors = PurchaseVendor::all();

        return $dataTable->render('purchase::vendor-credits.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(Request $request)
    {

        $this->id = request('id');
        $this->getbill = PurchaseBill::where('id', $this->id)->first();
        $this->vendor = PurchaseVendor::all();
        $this->purchaseSetting = PurchaseSetting::first();
        $this->zero = '';
        $this->lastVendorCredit = PurchaseVendorCredit::lastVendorCreditNumber() + 1;
        $this->products = PurchaseProduct::all();
        $this->categories = ProductCategory::all();
        $this->units = UnitType::all();
        $this->taxes = Tax::all();
        $this->currencies = Currency::all();
        $this->bills = PurchaseBill::get();
        $this->orderId = PurchaseBill::where('id', $request->id)->pluck('purchase_order_id')->toArray();
        $this->items = PurchaseItem::with('taxes')->whereIn('purchase_order_id', $this->orderId)->get();
        $this->order = PurchaseOrder::where('id', $this->orderId)->first();
        $this->companyCurrency = Currency::where('id', company()->currency_id)->first();

        $items = $this->items->filter(function ($value, $key) {
            return $value->type == 'item';
        });

        /** @phpstan-ignore-next-line */
        $tax = $this->items->filter(function ($value, $key) {
            return $value->type == 'tax';
        });

        $this->totalTax = $tax->sum('amount');
        $this->discount = $this->order->discount;
        $this->discountType = $this->order->discount_type;

        if ($this->discountType == 'percent') {
            $this->totalDiscount = $items->sum('amount') * $this->discount / 100;
        }

        if ($this->discountType == 'fixed') {
            $this->totalDiscount = $this->discount;
        }

        if (request()->ajax()) {
            $html = view('purchase::vendor-credits.ajax.create', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'purchase::vendor-credits.ajax.create';

        return view('purchase::vendor-credits.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $items = $request->item_name;
        $cost_per_item = $request->cost_per_item;
        $quantity = $request->quantity;
        $amount = $request->amount;

        if (!empty($items)) {
            foreach ($items as $itm) {
                if (is_null($itm)) {
                    return Reply::error(__('messages.itemBlank'));
                }
            }
        }
        else {
            return Reply::error(__('messages.addItem'));
        }

        foreach ($quantity as $qty) {
            if (!is_numeric($qty) && (intval($qty) < 1)) {
                return Reply::error(__('messages.quantityNumber'));
            }
        }

        foreach ($cost_per_item as $rate) {
            if (!is_numeric($rate)) {
                return Reply::error(__('messages.unitPriceNumber'));
            }
        }

        foreach ($amount as $amt) {
            if (!is_numeric($amt)) {
                return Reply::error(__('messages.amountNumber'));
            }
        }

        $vendorCredit = new PurchaseVendorCredit();
        $vendorCredit->vendor_id = $request->vendor_id;
        $vendorCredit->credit_note_no = $request->credit_note_no;
        $vendorCredit->credit_date = Carbon::createFromFormat($this->company->date_format, $request->vendor_credit_date)->format('Y-m-d');
        $vendorCredit->sub_total = $request->sub_total;
        $vendorCredit->total = $request->total;
        $vendorCredit->currency_id = $request->currency_id;
        $vendorCredit->note = trim_editor($request->note);
        $vendorCredit->discount = round($request->discount_value, 2);
        $vendorCredit->discount_type = $request->discount_type;
        $vendorCredit->bill_id = $request->billId;
        $vendorCredit->save();

        $redirectUrl = urldecode($request->redirect_url);

        if ($redirectUrl == '') {
            $redirectUrl = route('vendor-credits.index');
        }

        return Reply::redirect($redirectUrl, __('messages.recordSaved'));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {

        $this->vendorCredit = PurchaseVendorCredit::with('items', 'currency', 'unit', 'bills', 'bills.order.items.taxes', 'vendors')->findOrFail($id);
        if ($this->vendorCredit->discount > 0) {
            if ($this->vendorCredit->discount_type == 'percent') {
                $this->discount = (($this->vendorCredit->discount / 100) * $this->vendorCredit->sub_total);
            }
            else {
                $this->discount = $this->vendorCredit->discount;
            }
        }
        else {
            $this->discount = 0;
        }

        $taxList = array();

        $items = PurchaseVendorItem::whereNotNull('taxes')
            ->where('credit_id', $this->vendorCredit->id)
            ->get();

        $this->paidAmount = $this->vendorCredit->getPaidAmount();

        foreach ($items as $item) {

            foreach (json_decode($item->taxes) as $tax) {
                $this->tax = PurchaseVendorItem::taxbyid($tax)->first();

                if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {

                    if ($this->vendorCredit->calculate_tax == 'after_discount' && $this->discount > 0) {
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($item->amount - ($item->amount / $this->vendorCredit->sub_total) * $this->discount) * ($this->tax->rate_percent / 100);

                    }
                    else {
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $item->amount * ($this->tax->rate_percent / 100);
                    }

                }
                else {
                    if ($this->vendorCredit->calculate_tax == 'after_discount' && $this->discount > 0) {
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($item->amount - ($item->amount / $this->vendorCredit->sub_total) * $this->discount) * ($this->tax->rate_percent / 100));

                    }
                    else {
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + ($item->amount * ($this->tax->rate_percent / 100));
                    }
                }
            }
        }

        $this->taxes = $taxList;


        $this->settings = company();
        $this->vendorCreditSetting = invoice_setting();

        $this->view = 'purchase::vendor-credits.ajax.overview';
        $tab = request('tab');

        switch ($tab) {
        case 'history':
            return $this->history($id);
        default:
            $this->view = 'purchase::vendor-credits.ajax.overview';
            break;
        }

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->activeTab = $tab ?: 'overview';

        return view('purchase::vendor-credits.show', $this->data);

    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Request $request, $id)
    {
        $this->vendorCredit = PurchaseVendorCredit::with('vendors', 'items', 'items.purchaseVendorCreditItemImage', 'bills', 'bills.order.items.taxes')->findOrFail($id);
        $this->vendor = PurchaseVendor::all();
        $this->purchaseSetting = PurchaseSetting::first();
        $this->invoiceSetting = invoice_setting();
        $this->pageTitle = $this->vendorCredit->vendors->primary_name;
        $this->currencies = Currency::all();
        $this->categories = ProductCategory::all();
        $this->units = UnitType::all();
        $this->bills = PurchaseBill::get();
        $this->products = Product::all();
        $this->zero = '';
        $this->lastVendorCredit = PurchaseVendorCredit::lastVendorCreditNumber() + 1;
        $this->getbill = PurchaseBill::where('id', $id)->first();
        $this->taxes = Tax::all();
        $this->companyCurrency = Currency::where('id', company()->currency_id)->first();

        if (request()->ajax()) {

            $html = view('purchase::vendor-credits.ajax.edit', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'purchase::vendor-credits.ajax.edit';

        return view('purchase::vendor-credits.create', $this->data);

    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(UpdateVendorCreditRequest $request, $id)
    {
        $items = $request->item_name;
        $cost_per_item = $request->cost_per_item;
        $hsn_sac_code = $request->hsn_sac_code;
        $quantity = $request->quantity;
        $amount = $request->amount;
        $itemsSummary = $request->item_summary;
        $tax = $request->taxes;

        $vendorCredit = PurchaseVendorCredit::findOrFail($id);
        $vendorCredit->credit_date = Carbon::createFromFormat($this->company->date_format, $request->vendor_credit_date)->format('Y-m-d');
        $vendorCredit->currency_id = $request->currency_id;
        $vendorCredit->note = trim_editor($request->note);

        $vendorCredit->save();

        return Reply::redirect(route('vendor-credits.show', $vendorCredit->id), __('messages.updateSuccess'));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $vendorCreditDelete = PurchaseVendorCredit::findOrFail($id);
        $vendorCreditDelete->delete();

        return Reply::successWithData(__('messages.deleteSuccess'), ['redirectUrl' => route('vendor-credits.index')]);
    }

    public function addItem(Request $request)
    {
        $this->items = Product::findOrFail($request->id);

        $this->invoiceSetting = invoice_setting();

        $exchangeRate = Currency::findOrFail($request->currencyId);

        if (!is_null($exchangeRate) && !is_null($exchangeRate->exchange_rate)) {
            if ($this->items->total_amount != '') {
                /** @phpstan-ignore-next-line */
                $this->items->price = floor($this->items->total_amount * $exchangeRate->exchange_rate);
            }
            else {

                $this->items->price = floatval($this->items->price) * floatval($exchangeRate->exchange_rate);
            }
        }
        else {
            if ($this->items->total_amount != '') {
                $this->items->price = $this->items->total_amount;
            }
        }

        $this->items->price = number_format((float)$this->items->price, 2, '.', '');
        $this->taxes = Tax::all();
        $this->units = UnitType::all();
        $view = view('purchase::vendor-credits.ajax.add_item', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
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

        PurchaseVendorCredit::whereIn('id', explode(',', $request->row_ids))->delete();

        return true;
    }

    public function download($id)
    {
        $this->vendorCredit = PurchaseVendorCredit::with('items', 'currency', 'unit', 'bills', 'bills.order.items.taxes')->findOrFail($id);
        $pdfOption = $this->domPdfObjectForDownload($id);
        $pdf = $pdfOption['pdf'];
        $filename = $pdfOption['fileName'];

        return $pdf->download($filename . '.pdf');
    }

    public function domPdfObjectForDownload($id)
    {
        $this->invoiceSetting = invoice_setting();
        $this->proposal = PurchaseVendorCredit::with('items', 'vendors', 'currency', 'unit')->findOrFail($id);
        App::setLocale($this->invoiceSetting->locale ?? 'en');
        Carbon::setLocale($this->invoiceSetting->locale ?? 'en');

        if ($this->proposal->discount > 0) {
            if ($this->proposal->discount_type == 'percent') {
                $this->discount = (($this->proposal->discount / 100) * $this->proposal->sub_total);
            }
            else {
                $this->discount = $this->proposal->discount;
            }
        }
        else {
            $this->discount = 0;
        }

        $taxList = array();

        $items = PurchaseVendorItem::whereNotNull('taxes')
            ->where('credit_id', $this->proposal->id)
            ->get();
        $this->invoiceSetting = invoice_setting();

        foreach ($items as $item) {

            foreach (json_decode($item->taxes) as $tax) {
                $this->tax = PurchaseVendorItem::taxbyid($tax)->first();

                if ($this->tax) {
                    if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {

                        if ($this->proposal->calculate_tax == 'after_discount' && $this->discount > 0) {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($item->amount - ($item->amount / $this->proposal->sub_total) * $this->discount) * ($this->tax->rate_percent / 100);

                        }
                        else {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $item->amount * ($this->tax->rate_percent / 100);
                        }

                    }
                    else {
                        if ($this->proposal->calculate_tax == 'after_discount' && $this->discount > 0) {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($item->amount - ($item->amount / $this->proposal->sub_total) * $this->discount) * ($this->tax->rate_percent / 100));

                        }
                        else {
                            $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + ($item->amount * ($this->tax->rate_percent / 100));
                        }
                    }
                }
            }
        }

        $this->taxes = $taxList;

        $this->company = company();

        $pdf = app('dompdf.wrapper');

        $pdf->setOption('enable_php', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        $pdf->loadView('purchase::vendor-credits.pdf.file', $this->data);

        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->getCanvas();
        $canvas->page_text(530, 820, 'Page {PAGE_NUM} of {PAGE_COUNT}', null, 10);
        $filename = __('purchase::app.menu.vendorCredit') . '-' . $this->proposal->id;

        return [
            'pdf' => $pdf,
            'fileName' => $filename
        ];
    }

    public function getBills(Request $request)
    {
        if ($request->id != 'null') {
            $getBills = PurchaseBill::where('purchase_vendor_id', $request->id)->where('status', 'paid')->get();
        }
        else {
            $getBills = PurchaseBill::get();
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $getBills]);
    }

    public function deleteCreditItemImage(Request $request)
    {
        $item = PurchaseVendorCreditItemImage::where('vendor_item_id', $request->invoice_item_id)->first();

        if ($item) {
            Files::deleteFile($item->hashname, 'vendorCredit-files/' . $item->id . '/');
            $item->delete();
        }

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function addBillItem(Request $request)
    {

        $this->orderId = PurchaseBill::where('id', $request->id)->pluck('purchase_order_id')->toArray();
        $this->items = PurchaseItem::whereIn('purchase_order_id', $this->orderId)->get();
        $this->invoiceSetting = invoice_setting();

        $exchangeRate = Currency::findOrFail($request->currencyId);

        if (!is_null($exchangeRate) && !is_null($exchangeRate->exchange_rate)) {
            if ($this->items[0]->total_amount != '') {
                /** @phpstan-ignore-next-line */
                $this->items->price = floor($this->items[0]->total_amount * $exchangeRate->exchange_rate);
            }
            else {

                $this->items->price = floatval($this->items[0]->price) * floatval($exchangeRate->exchange_rate);
            }
        }
        else {
            if ($this->items->total_amount != '') {
                $this->items->price = $this->items->total_amount;
            }
        }

        $this->items->price = number_format((float)$this->items->price, 2, '.', '');
        $this->taxes = Tax::all();
        $this->units = UnitType::all();
        $view = view('purchase::vendor-credits.ajax.add_bill_item', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function history($id)
    {
        $tab = request('tab');
        $this->activeTab = $tab ?: 'overview';
        $this->history = PurchaseVendorCreditHistory::where('purchase_credit_id', $id)->orderByDesc('id')->get();
        $this->view = 'purchase::vendor-credits.ajax.history';

        return view('purchase::vendor-credits.show', $this->data);
    }

    public function applyToBill($id)
    {
        $this->pageTitle = __('purchase::app.applyToBill');
        $this->vendorCredit = PurchaseVendorCredit::findOrFail($id);
        $this->nonPaidInvoices = PurchaseBill::select('purchase_bills.*')->where('credit_note', 0)
            ->where(function ($q) {
                $q->where('purchase_bills.status', 'open')
                    ->orWhere('purchase_bills.status', 'partially_paid');
            })
            ->where('purchase_vendor_id', $this->vendorCredit->vendor_id);

        $this->nonPaidInvoices = $this->nonPaidInvoices->with('purchasePaymentBills')->get();

        if (request()->ajax()) {
            $html = view('purchase::vendor-credits.ajax.apply_to_bill', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'purchase::vendor-credits.ajax.apply_to_bill';

        return view('purchase::vendor-credits.create', $this->data);
    }

    public function applyBillCredit(Request $request, $id)
    {
        $totalCreditAmount = 0.00;

        foreach ($request->invoices as $invoice) {
            $totalCreditAmount += $invoice['value'];
        }

        if ($totalCreditAmount == 0) {
            return Reply::error(__('messages.pleaseEnterCreditAmount'));
        }

        $creditNote = PurchaseVendorCredit::findOrFail($id);
        $creditTotalAmount = 0.00;

        if ((float)$request->remainingAmount <= 0) {
            $creditNote->status = 'closed';
        }

        foreach ($request->invoices as $invoice) {

            if ($invoice['value'] !== '0' && !is_null($invoice['value'])) {
                $creditTotalAmount += (float)$invoice['value'];

                $reqInvoice = PurchaseBill::findOrFail($invoice['invoiceId']);
                $this->makePayment($id, $invoice['invoiceId'], (float)$invoice['value']);

                if ($reqInvoice->total > $reqInvoice->amountPaid()) {
                    $reqInvoice->status = 'partially_paid';
                }
                else {
                    $reqInvoice->status = 'paid';
                }

                $dueAmount = $reqInvoice->amountDue($creditNote->vendor_id);
                $reqInvoice->due_amount = $dueAmount;
                $reqInvoice->save();

            }
        }

        $creditNote->save();

        return Reply::redirect(route('vendor-credits.show', $creditNote->id), __('messages.creditNoteAppliedSuccessfully'));
    }

    public function makePayment($creditNoteId, $invoiceId, $amount)
    {
        $creditNote = PurchaseVendorCredit::findOrFail($creditNoteId);
        $payment = new PurchasePaymentBill();
        $payment->purchase_bill_id = $invoiceId;
        $payment->purchase_vendor_credits_id = $creditNoteId;
        $payment->total_paid = $amount;
        $payment->gateway = 'Credit Note';
        $payment->purchase_vendor_id = $creditNote->vendor_id;
        $payment->save();
        $remainingAmount = $creditNote->creditAmountRemaining();
        event(new VendorCreditPaymentMade($creditNote, $amount, $remainingAmount));
    }

}
