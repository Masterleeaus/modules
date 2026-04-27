<?php

namespace Modules\Purchase\Http\Controllers;

use Carbon\Carbon;
use App\Helper\Reply;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;
use Modules\Purchase\Entities\PurchaseSetting;
use Modules\Purchase\Entities\PurchaseVendor;
use App\Http\Controllers\AccountBaseController;
use Modules\Purchase\DataTables\PurchaseBillDataTable;
use Modules\Purchase\DataTables\VendorDataTable;
use Modules\Purchase\DataTables\VendorNotesDataTable;
use Modules\Purchase\Entities\PurchaseBill;
use Modules\Purchase\Entities\PurchasePaymentBill;
use Modules\Purchase\Entities\PurchaseVendorCategory;
use Modules\Purchase\Entities\PurchaseVendorCredit;
use Modules\Purchase\Entities\PurchaseVendorPayment;
use Modules\Purchase\Entities\PurchaseVendorHistory;
use Modules\Purchase\Entities\PurchasePaymentHistory;
use Modules\Purchase\Http\Requests\Vendor\StoreRequest;
use Modules\Purchase\DataTables\PurchaseContactsDataTable;
use Modules\Purchase\DataTables\PurchaseOrderDataTable;
use Modules\Purchase\DataTables\VendorPaymentDataTable;

class PurchaseVendorController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('purchase::app.menu.vendor');
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function index(VendorDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_vendor');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));
        $this->pageTitle = __('purchase::app.menu.vendor');
        $this->categories = PurchaseVendorCategory::all();
        return $dataTable->render('purchase::vendors.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $addPermission = user()->permission('add_vendor');
        abort_403(!in_array($addPermission, ['all', 'added']));

        $this->pageTitle = __('purchase::app.menu.addVendor');
        $this->currencies = Currency::all();
        $this->categories = PurchaseVendorCategory::all();

        if (request()->ajax()) {
            $html = view('purchase::vendors.ajax.create', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'purchase::vendors.ajax.create';

        return view('purchase::vendors.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(StoreRequest $request)
    {
        $addPermission = user()->permission('add_vendor');
        abort_403(!in_array($addPermission, ['all', 'added']));

        $vendor = new PurchaseVendor();
        $vendor->primary_name = $request->primary_name;
        $vendor->company_name = $request->company_name;
        $vendor->category_id = $request->category_id;
        $vendor->email = $request->email;
        $vendor->phone = $request->phone;
        $vendor->website = $request->website;
        $vendor->opening_balance = $request->opening_balance ?? 0;
        $vendor->currency_id = $request->currency_id;
        $vendor->shipping_address = $request->shipping_address;
        $vendor->billing_address = $request->billing_address;
        $vendor->save();

        if (request()->add_more == 'true') {
            $html = $this->create();

            return Reply::successWithData(__('messages.recordSaved'), ['html' => $html, 'add_more' => true]);
        }

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('vendors.index')]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $this->vendor = PurchaseVendor::findOrFail($id);
        $this->currency = Currency::where('id', '=', $this->vendor->currency_id)->first();
        $this->startDate = now()->startOfYear();
        $this->endDate = now()->endOfYear();
        $startDate = $this->startDate->toDateString();
        $endDate = $this->endDate->toDateString();
        $this->currencyFormatSetting = currency_format_setting();
        $this->earningChartData = $this->vendorPaymentsGraph($startDate, $endDate, $this->vendor->id);

        $this->totalBillAmount = PurchaseBill::where('purchase_vendor_id', $id)->sum('total');

        $this->totalPaymentAmount = PurchasePaymentBill::where('purchase_vendor_id', $id)->sum('total_paid');

        $this->totalCreditAmount = PurchaseVendorCredit::where('vendor_id', $id)->sum('total');

        $this->usedCredits = PurchasePaymentBill::where('purchase_vendor_id', $id)->where('gateway', 'Credit Note')->sum('total_paid');

        $this->unusedAmt = $this->totalCreditAmount - $this->usedCredits;

        $this->pageTitle = $this->vendor->primary_name;

        $this->view = 'purchase::vendors.ajax.overview';
        $tab = request('tab');

        switch ($tab) {
        case 'notes':
            return $this->notes();
        case 'contacts':
            return $this->contacts();
        case 'purchaseOrders':
            return $this->purchaseOrders();
        case 'bills':
            return $this->bills();
        case 'payments':
            return $this->payments();
        case 'history':
            return $this->history($id);
        default:
            $this->currency = Currency::where('id', '=', $this->vendor->currency_id)->first();
            $this->view = 'purchase::vendors.ajax.overview';
            break;
        }

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->activeTab = $tab ?: 'overview';

        return view('purchase::vendors.show', $this->data);
    }

    public function vendorPaymentsGraph($startDate, $endDate, $vendor_id)
    {
        $vendorPayments = PurchaseVendorPayment::where('purchase_vendor_id', $vendor_id);
        $vendorPayments = $vendorPayments->whereBetween('purchase_vendor_payments.payment_date', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);

        $vendorPayments = $vendorPayments->orderBy('payment_date', 'ASC')
            ->get([
                DB::raw('DATE_FORMAT(payment_date,"%d-%M-%y") as date'),
                DB::raw('YEAR(payment_date) year, MONTH(payment_date) month'),
                DB::raw('received_payment as total')
            ]);

        $incomes = [];

        foreach ($vendorPayments as $invoice) {
            if (!isset($incomes[$invoice->payment_date])) {
                $incomes[$invoice->date] = 0;
            }

            $incomes[$invoice->date] += round($invoice->total, 2);
        }

        $dates = array_keys($incomes);
        $graphData = [];

        foreach ($dates as $date) {
            $graphData[] = [
                'date' => $date,
                'total' => isset($incomes[$date]) ? round($incomes[$date], 2) : 0,
            ];
        }

        usort($graphData, function ($a, $b) {
            $t1 = strtotime($a['date']);
            $t2 = strtotime($b['date']);

            return $t1 - $t2;
        });

        $graphData = collect($graphData);

        $data['labels'] = $graphData->pluck('date');
        $data['values'] = $graphData->pluck('total')->toArray();
        $data['colors'] = [$this->appTheme->header_color];
        $data['name'] = __('app.earnings');

        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->pageTitle = __('purchase::app.menu.editVendor');
        $this->vendor = PurchaseVendor::findOrFail($id);

        $this->editPermission = user()->permission('edit_vendor');
        abort_403(!($this->editPermission == 'all'
            || ($this->editPermission == 'added' && $this->vendor->added_by == user()->id)));

        $this->currencies = Currency::all();
        $this->categories = PurchaseVendorCategory::all();
        if (request()->ajax()) {
            $html = view('purchase::vendors.ajax.edit', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'purchase::vendors.ajax.edit';

        return view('purchase::vendors.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(StoreRequest $request, $id)
    {
        $this->editPermission = user()->permission('edit_vendor');
        $vendor = PurchaseVendor::findOrFail($id);
        abort_403(!($this->editPermission == 'all'
            || ($this->editPermission == 'added' && $vendor->added_by == user()->id)));

        $vendor->primary_name = $request->primary_name;
        $vendor->company_name = $request->company_name;
        $vendor->category_id = $request->category_id;
        $vendor->email = $request->email;
        $vendor->phone = $request->phone;
        $vendor->website = $request->website;
        $vendor->opening_balance = $request->opening_balance;
        $vendor->currency_id = $request->currency_id;
        $vendor->shipping_address = $request->shipping_address;
        $vendor->billing_address = $request->billing_address;
        $vendor->last_updated_by = user()->id;
        $vendor->save();

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('vendors.index')]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $vendor = PurchaseVendor::findOrFail($id);
        $this->deletePermission = user()->permission('delete_vendor');
        abort_403(!($this->deletePermission == 'all'
            || ($this->deletePermission == 'added' && $vendor->added_by == user()->id)));
        $vendor->delete();

        return Reply::successWithData(__('messages.deleteSuccess'), ['redirectUrl' => route('vendors.index')]);
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
        abort_403(user()->permission('delete_job') != 'all');
        PurchaseVendor::whereIn('id', explode(',', $request->row_ids))->delete();

        return true;
    }

    public function notes()
    {
        $dataTable = new VendorNotesDataTable();

        $tab = request('tab');

        $this->activeTab = $tab ?: 'overview';

        $this->view = 'purchase::vendors.ajax.notes';

        return $dataTable->render('purchase::vendors.show', $this->data);

    }

    public function contacts()
    {
        $dataTable = new PurchaseContactsDataTable();

        $tab = request('tab');

        $this->activeTab = $tab ?: 'overview';

        $this->view = 'purchase::vendors.ajax.contacts';

        return $dataTable->render('purchase::vendors.show', $this->data);
    }

    public function history($id)
    {
        $tab = request('tab');

        $this->activeTab = $tab ?: 'history';

        $vendorVariable = PurchaseVendorHistory::where('purchase_vendor_id', $id)
            ->selectRaw('purchase_vendor_id, "null" as purchase_order_id, "null" as purchase_bill_id, "null" as purchase_payment_id, "null" as purchase_credit_id, "null" as purchase_order, purchase_vendor_notes_id, "null" as bill_date, user_id, "null" as amount, label, details, purchase_vendor_histories.created_at, "Vendor" as type');
        $orderVariable = DB::table('purchase_order_histories')
            ->where('purchase_vendors.id', $id)
            ->join('purchase_vendors', 'purchase_vendors.id', 'purchase_order_histories.purchase_vendor_id')
            ->selectRaw('purchase_vendor_id, purchase_order_id, "null" as purchase_bill_id, "null" as purchase_payment_id, "null" as purchase_credit_id, "null" as purchase_order, "null" as purchase_vendor_notes_id, "null" as bill_date, user_id, "null" as amount, label, details, purchase_order_histories.created_at, "Order" as type')
            ->where('purchase_order_histories.company_id', company()->id);
        $billVariable = DB::table('purchase_bill_histories')
            ->where('purchase_vendors.id', $id)
            ->join('purchase_vendors', 'purchase_vendors.id', 'purchase_bill_histories.purchase_vendor_id')
            ->selectRaw('purchase_vendor_id, "null" as purchase_order_id, purchase_bill_id, "null" as purchase_payment_id, "null" as purchase_credit_id, purchase_order, "null" as purchase_vendor_notes_id, bill_date, user_id, amount, label, details, purchase_bill_histories.created_at, "bill" as type')
            ->where('purchase_bill_histories.company_id', company()->id);
        $creditVariable = DB::table('purchase_vendor_credit_histories')
            ->where('purchase_vendors.id', $id)
            ->join('purchase_vendors', 'purchase_vendors.id', 'purchase_vendor_credit_histories.purchase_vendor_id')
            ->selectRaw('purchase_vendor_id, "null" as purchase_order_id, "null" as purchase_bill_id, "null" as purchase_payment_id, purchase_credit_id, "null" as purchase_order, "null" as purchase_vendor_notes_id, "null" as bill_date, user_id, amount, label, details, purchase_vendor_credit_histories.created_at, "credit" as type')
            ->where('purchase_vendor_credit_histories.company_id', company()->id);
        $paymentVariable = PurchasePaymentHistory::join('purchase_vendors', 'purchase_vendors.id', 'purchase_payment_histories.purchase_vendor_id')
            ->where('purchase_vendors.id', $id)
            ->with('user')
            ->selectRaw('purchase_vendor_id, "null" as purchase_order_id, "null" as purchase_bill_id, purchase_payment_id, "null" as purchase_credit_id, purchase_order, "null" as purchase_vendor_notes_id, "null" as bill_date, user_id, amount, label, details, purchase_payment_histories.created_at, "Payment" as type')
            ->where('purchase_payment_histories.company_id', company()->id)
            ->union($vendorVariable)
            ->union($orderVariable)
            ->union($billVariable)
            ->union($creditVariable)
            ->orderByDesc('created_at')->get();

        $this->history = $paymentVariable;
        $this->view = 'purchase::vendors.ajax.history';

        return view('purchase::vendors.show', $this->data);
    }

    public function purchaseOrders()
    {
        $dataTable = new PurchaseOrderDataTable();

        $tab = request('tab');
        $this->activeTab = $tab ?: 'overview';

        $this->view = 'purchase::vendors.ajax.purchase-orders';

        return $dataTable->render('purchase::vendors.show', $this->data);
    }

    public function bills()
    {
        $dataTable = new PurchaseBillDataTable();
        $tab = request('tab');
        $this->activeTab = $tab ?: 'overview';

        $this->view = 'purchase::vendors.ajax.bills';

        return $dataTable->render('purchase::vendors.show', $this->data);
    }

    public function payments()
    {
        $dataTable = new VendorPaymentDataTable();

        $tab = request('tab');
        $this->activeTab = $tab ?: 'overview';

        $this->view = 'purchase::vendors.ajax.payments';

        return $dataTable->render('purchase::vendors.show', $this->data);
    }

}
