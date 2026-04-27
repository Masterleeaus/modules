<?php

namespace Modules\Purchase\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tax;
use App\Helper\Reply;
use App\Models\Currency;
use App\Models\UnitType;
use Illuminate\Http\Request;
use App\Models\CompanyAddress;
use Illuminate\Support\Facades\App;
use App\Models\PaymentGatewayCredentials;
use Modules\Purchase\Entities\PurchaseBill;
use Modules\Purchase\Entities\PurchaseItem;
use Illuminate\Contracts\Support\Renderable;
use Modules\Purchase\Entities\PurchaseOrder;
use Modules\Purchase\Entities\PurchaseVendor;
use Modules\Purchase\Http\Requests\StoreBill;
use Modules\Purchase\Entities\PurchaseSetting;
use App\Http\Controllers\AccountBaseController;
use Modules\Purchase\Events\NewPurchaseBillEvent;
use Modules\Purchase\Entities\PurchaseBillHistory;
use Modules\Purchase\Entities\PurchasePaymentHistory;
use Modules\Purchase\DataTables\PurchaseBillDataTable;

class PurchaseBillController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'purchase::app.menu.bill';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function index(PurchaseBillDataTable $dataTable)
    {
        $this->viewBillPermission = user()->permission('view_bill');
        $this->addBillPermission = user()->permission('add_bill');
        $this->vendors = PurchaseVendor::with('currency')->get();

        abort_403(!in_array($this->viewBillPermission, ['all', 'added', 'owned', 'both']));
        $this->pageTitle = 'purchase::app.menu.bills';

        return $dataTable->render('purchase::bills.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $this->pageTitle = __('purchase::app.menu.createBill');
        $this->lastBill = PurchaseBill::lastPurchaseBillNumber() + 1;
        $this->purchaseSetting = PurchaseSetting::first();
        $this->zero = '';
        $this->purchaseVendors = PurchaseVendor::all();
        $this->units = UnitType::all();
        $this->taxes = Tax::all();
        $this->paymentGateway = PaymentGatewayCredentials::first();
        $this->companyCurrency = Currency::where('id', company()->currency_id)->first();
        $this->currencies = Currency::all();
        $this->purchaseOrder = [];
        $this->vendorID = request()->bill_vendor_id;

        if (strlen($this->lastBill) < $this->purchaseSetting->bill_number_digit) {
            $condition = $this->purchaseSetting->bill_number_digit - strlen($this->lastBill);

            for ($i = 0; $i < $condition; $i++) {
                $this->zero = '0' . $this->zero;
            }
        }

        if (request('order') != '') {
            $this->purchaseOrderId = request()->order;
            $this->type = 'order';
            $this->purchaseOrder = PurchaseOrder::with('vendor')->find($this->purchaseOrderId);
        }
        if (request()->ajax()) {
            $html = view('purchase::bills.ajax.create', $this->data)->render();
            
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'purchase::bills.ajax.create';

        return view('purchase::bills.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(StoreBill $request)
    {
        $bill = new PurchaseBill();
        $bill->purchase_bill_number = $request->bill_number;
        $bill->purchase_vendor_id = $request->vendor_id;
        $bill->bill_date = Carbon::createFromFormat($this->company->date_format, $request->issue_date)->format('Y-m-d');
        $bill->purchase_order_id = $request->purchase_order_id;
        $bill->discount = $request->discount_value;
        $bill->sub_total = $request->sub_total;
        $bill->total = $request->total;
        $bill->discount_type = $request->discount_type;
        $bill->status = $request->type;
        $bill->note = $request->note;
        $bill->save();


        return reply::success(__('messages.recordSaved'));

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $this->viewBillPermission = user()->permission('view_bill');
        $this->editBillPermission = user()->permission('edit_bill');
        $this->deleteBillPermission = user()->permission('delete_bill');
        $this->purchaseBill = PurchaseBill::with('purchaseVendor', 'order')->findOrFail($id);
        $this->orderCurrency = PurchaseOrder::where('id', $this->purchaseBill->order->id)->first();
        abort_403(!($this->viewBillPermission == 'all' || $this->viewBillPermission == 'added' && user()->id == $this->purchaseBill->added_by));
        $this->purchaseOrder = PurchaseOrder::with('items', 'items.taxes', 'items.purchaseItemImage')->findOrFail($this->purchaseBill->purchase_order_id);
        $this->settings = company();

        $this->discount = 0;

        if ($this->purchaseOrder->discount > 0) {
            if ($this->purchaseOrder->discount_type == 'percent') {
                $this->discount = (($this->purchaseOrder->discount / 100) * $this->purchaseOrder->sub_total);
            }
            else {
                $this->discount = $this->purchaseOrder->discount;
            }
        }

        $taxList = array();

        foreach ($this->purchaseOrder->items as $item) {
            if (!is_null($item->taxes)) {
                foreach ($item->taxes as $tax) {
                    if (!isset($taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'])) {

                        if ($this->purchaseOrder->calculate_tax == 'after_discount' && $this->discount > 0) {
                            $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] = ($item->amount - ($item->amount / $this->purchaseOrder->sub_total) * $this->discount) * ($tax->rate_percent / 100);

                        }
                        else {
                            $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] = $item->amount * ($tax->rate_percent / 100);
                        }

                    }
                    else {
                        if ($this->purchaseOrder->calculate_tax == 'after_discount' && $this->discount > 0) {
                            $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] = $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] + (($item->amount - ($item->amount / $this->purchaseOrder->sub_total) * $this->discount) * ($tax->rate_percent / 100));

                        }
                        else {
                            $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] = $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] + ($item->amount * ($tax->rate_percent / 100));
                        }
                    }
                }
            }

        }

        $this->taxes = $taxList;

        $this->view = 'purchase::bills.ajax.overview';
        $tab = request('tab');

        switch ($tab) {
        case 'history':
            return $this->history($id);
        default:
            $this->view = 'purchase::bills.ajax.overview';
            break;
        }

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->activeTab = $tab ?: 'overview';

        return view('purchase::bills.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->editBillPermission = user()->permission('edit_bill');
        $this->purchaseBill = PurchaseBill::with('purchaseVendor', 'order')->findOrFail($id);
        abort_403(
            !($this->editBillPermission == 'all'
                || $this->editBillPermission == 'added' && user()->id == $this->purchaseBill->added_by)
        );

        $this->purchaseItems = PurchaseItem::with('unit', 'taxes', 'purchaseItemImage')->where('purchase_order_id', $this->purchaseBill->purchase_order_id)->get();
        $this->pageTitle = $this->purchaseBill->bill_number;
        $this->zero = '';
        $this->purchaseSetting = PurchaseSetting::first();

        if (request()->ajax()) {
            $html = view('purchase::bills.ajax.edit', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'purchase::bills.ajax.edit';

        return view('purchase::bills.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {

        $purchaseBill = PurchaseBill::findOrFail($id);
        $purchaseBill->bill_Date = Carbon::createFromFormat($this->company->date_format, $request->issue_date)->format('Y-m-d');
        $purchaseBill->note = $request->note;

        if ($request->type) {
            $purchaseBill->status = $request->type;
        }

        $purchaseBill->save();

        return reply::success(__('messages.updateSuccess'));
    }

    public function vendorPurchaseOrders()
    {
        $purchaseOrderId = PurchaseBill::pluck('purchase_order_id');
        $purchaseOrder = PurchaseOrder::whereNotIn('id', $purchaseOrderId)->where('vendor_id', request()->vendor_id)->get();

        return Reply::dataOnly(['data' => $purchaseOrder]);
    }

    public function purchaseOrderProducts()
    {
        $this->purchaseOrder = PurchaseOrder::where('id', request()->purchase_order_id)->first();
        $this->purchaseItems = PurchaseItem::with('unit', 'taxes', 'purchaseOrder', 'purchaseItemImage')->where('purchase_order_id', request()->purchase_order_id)->get();
        $view = view('purchase::bills.ajax.add_item', $this->data)->render();

        return reply::dataOnly(['view' => $view]);
    }

    public function sendBill($billId)
    {
        $purchaseBill = PurchaseBill::with(['purchaseVendor'])->findOrFail($billId);

        if ($purchaseBill->purchase_vendor_id != null && $purchaseBill->purchase_vendor_id != '') {
            $notifyUser = $purchaseBill->purchaseVendor;
        }

        if (isset($notifyUser) && !is_null($notifyUser)) {
            event(new NewPurchaseBillEvent($purchaseBill, $notifyUser));
        }

        return Reply::success(__('purchase::messages.billSentSuccessfully'));

    }

    public function download($id)
    {
        $this->invoiceSetting = invoice_setting();
        $this->purchaseSetting = PurchaseSetting::first();

        $this->purchaseBill = PurchaseBill::findOrFail($id);
        $this->viewPermission = user()->permission('view_bill');

        abort_403(!(
            $this->viewPermission == 'all'
            || ($this->viewPermission == 'added' && $this->purchaseBill->added_by == user()->id)
        ));

        App::setLocale($this->invoiceSetting->locale ?? 'en');
        Carbon::setLocale($this->invoiceSetting->locale ?? 'en');

        $pdfOption = $this->domPdfObjectForDownload($id, $this->purchaseBill);
        $pdf = $pdfOption['pdf'];
        $filename = $pdfOption['fileName'];

        return request()->view ? $pdf->stream($filename . '.pdf') : $pdf->download($filename . '.pdf');
    }

    public function domPdfObjectForConsoleDownload($id)
    {
        $this->invoiceSetting = invoice_setting();
        $this->purchaseBill = PurchaseBill::findOrFail($id);
        $this->purchaseOrder = PurchaseOrder::with('items', 'items.unit')->findOrFail($this->purchaseBill->purchase_order_id);

        if ($this->purchaseOrder->discount > 0) {
            if ($this->purchaseOrder->discount_type == 'percent') {
                $this->discount = (($this->purchaseOrder->discount / 100) * $this->purchaseOrder->sub_total);

            }
            else {
                $this->discount = $this->purchaseOrder->discount;
            }
        }
        else {
            $this->discount = 0;
        }

        $taxList = array();

        foreach ($this->purchaseOrder->items as $item) {
            if (!is_null($item->taxes)) {
                foreach ($item->taxes as $tax) {
                    if (!isset($taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'])) {

                        if ($this->purchaseOrder->calculate_tax == 'after_discount' && $this->discount > 0) {
                            $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] = ($item->amount - ($item->amount / $this->purchaseOrder->sub_total) * $this->discount) * ($tax->rate_percent / 100);

                        }
                        else {
                            $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] = $item->amount * ($tax->rate_percent / 100);
                        }

                    }
                    else {
                        if ($this->purchaseOrder->calculate_tax == 'after_discount' && $this->discount > 0) {
                            $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] = $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] + (($item->amount - ($item->amount / $this->purchaseOrder->sub_total) * $this->discount) * ($tax->rate_percent / 100));

                        }
                        else {
                            $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] = $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] + ($item->amount * ($tax->rate_percent / 100));
                        }
                    }
                }
            }

        }


        $this->taxes = $taxList;

        $this->company = $this->purchaseOrder->company;

        $this->invoiceSetting = $this->company->invoiceSetting;
        $this->defaultAddress = CompanyAddress::where('is_default', 1)->where('company_id', $this->order->company_id)->first();

        $pdf = app('dompdf.wrapper');
        $pdf->setOption('enable_php', true);
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        App::setLocale($this->invoiceSetting->locale ?? 'en');
        Carbon::setLocale($this->invoiceSetting->locale ?? 'en');
        $pdf->loadView('purchase::bills.pdf.' . $this->invoiceSetting->template, $this->data);

        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->getCanvas();
        $canvas->page_text(530, 820, 'Page {PAGE_NUM} of {PAGE_COUNT}', null, 10);

        $filename = $this->purchaseBill->bill_number;

        return [
            'pdf' => $pdf,
            'fileName' => $filename
        ];
    }

    public function domPdfObjectForDownload($id)
    {
        $this->invoiceSetting = invoice_setting();
        $this->purchaseSetting = PurchaseSetting::first();
        $this->purchaseBill = PurchaseBill::findOrFail($id);
        $this->purchaseOrder = PurchaseOrder::with('items', 'items.taxes', 'items.purchaseItemImage')->findOrFail($this->purchaseBill->purchase_order_id);

        App::setLocale($this->invoiceSetting->locale ?? 'en');
        Carbon::setLocale($this->invoiceSetting->locale ?? 'en');

        if ($this->purchaseOrder->discount > 0) {
            if ($this->purchaseOrder->discount_type == 'percent') {
                $this->discount = (($this->purchaseOrder->discount / 100) * $this->purchaseOrder->sub_total);

            }
            else {
                $this->discount = $this->purchaseOrder->discount;
            }
        }
        else {
            $this->discount = 0;
        }

        $taxList = array();

        foreach ($this->purchaseOrder->items as $item) {
            if (!is_null($item->taxes)) {
                foreach ($item->taxes as $tax) {
                    if (!isset($taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'])) {

                        if ($this->purchaseOrder->calculate_tax == 'after_discount' && $this->discount > 0) {
                            $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] = ($item->amount - ($item->amount / $this->purchaseOrder->sub_total) * $this->discount) * ($tax->rate_percent / 100);

                        }
                        else {
                            $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] = $item->amount * ($tax->rate_percent / 100);
                        }

                    }
                    else {
                        if ($this->purchaseOrder->calculate_tax == 'after_discount' && $this->discount > 0) {
                            $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] = $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] + (($item->amount - ($item->amount / $this->purchaseOrder->sub_total) * $this->discount) * ($tax->rate_percent / 100));

                        }
                        else {
                            $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] = $taxList[$tax->tax_name . ': ' . $tax->rate_percent . '%'] + ($item->amount * ($tax->rate_percent / 100));
                        }
                    }
                }
            }

        }

        $this->taxes = $taxList;

        $this->company = $this->purchaseOrder->company;

        $pdf = app('dompdf.wrapper');
        $pdf->setOption('enable_php', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        $pdf->loadView('purchase::bills.pdf.' . $this->invoiceSetting->template, $this->data);
        $filename = $this->purchaseBill->bill_number;

        return [
            'pdf' => $pdf,
            'fileName' => $filename
        ];
    }

    public function history($id)
    {
        $tab = request('tab');

        $this->activeTab = $tab ?: 'history';

        $billVariable = PurchaseBillHistory::where('purchase_bill_id', $id)
            ->selectRaw('purchase_order_id, purchase_bill_id, "null" as purchase_payment_id, "null" as purchase_credit_id, purchase_order, "null" as purchase_vendor_notes_id, bill_date, user_id, amount, label, details, purchase_bill_histories.created_at, "bill" as type');
        $paymentVariable = PurchasePaymentHistory::join('purchase_bills', 'purchase_bills.id', 'purchase_payment_histories.purchase_bill_id')
            ->where('purchase_bills.id', $id)
            ->with('user')
            ->selectRaw('"null" as purchase_order_id, purchase_bill_id, purchase_payment_id, "null" as purchase_credit_id, purchase_order, "null" as purchase_vendor_notes_id, "null" as bill_date, user_id, amount, label, details, purchase_payment_histories.created_at, "Payment" as type')
            ->where('purchase_payment_histories.company_id', company()->id)
            ->union($billVariable)
            ->orderByDesc('created_at')->get();

        $this->history = $paymentVariable;
        $this->view = 'purchase::bills.ajax.history';

        return view('purchase::bills.show', $this->data);
    }

}
