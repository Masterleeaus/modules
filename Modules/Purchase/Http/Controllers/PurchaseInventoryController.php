<?php

namespace Modules\Purchase\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tax;
use App\Helper\Reply;
use App\Models\Currency;
use App\Models\InvoiceItems;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Contracts\Support\Renderable;
use Modules\Purchase\Entities\PurchaseProduct;
use Modules\Purchase\Entities\PurchaseSetting;
use App\Http\Controllers\AccountBaseController;
use Modules\Purchase\Entities\PurchaseInventory;
use Modules\Purchase\Events\PurchaseInventoryEvent;
use Modules\Purchase\Entities\PurchaseStockAdjustment;
use Modules\Purchase\Entities\PurchaseInventoryHistory;
use Modules\Purchase\DataTables\PurchaseInventoryDataTable;
use Modules\Purchase\Entities\PurchaseStockAdjustmentReason;
use Modules\Purchase\Http\Requests\Inventory\StorePurchaseInventoryRequest;

class PurchaseInventoryController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'purchase::app.menu.inventory';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(PurchaseInventoryDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_inventory');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        $this->inventory = PurchaseInventory::all();

        return $dataTable->render('purchase::purchase-inventory.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $this->pageTitle = __('purchase::app.addInventory');
        $this->addPermission = user()->permission('add_inventory');
        abort_403(!in_array($this->addPermission, ['all', 'added']));

        if (request('inventory') != '') {
            $this->inventoryId = request('inventory');
            $this->purchaseInventory = PurchaseInventory::findOrFail($this->inventoryId);
        }

        $this->reasons = PurchaseStockAdjustmentReason::all();
        $this->categories = ProductCategory::all();
        $this->products = PurchaseProduct::where('type', '!=', 'service')->get();

        if (request()->ajax()) {
            $html = view('purchase::purchase-inventory.ajax.create', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'purchase::purchase-inventory.ajax.create';

        return view('purchase::purchase-inventory.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(StorePurchaseInventoryRequest $request)
    {
        $items = $request->product_id;
        $cost_per_item = $request->changed_value;
        $quantity = $request->quantity_on_hand;

        if (empty($items)) {
            return Reply::error(__('messages.addItem'));
        }

        if (!empty($quantity)) {
            foreach ($quantity as $qty) {

                if (is_null($qty)) {
                    return Reply::error(__('purchase::messages.inventory.itemBlank'));
                }

                if (!is_numeric($qty) && (intval($qty) < 1)) {
                    return Reply::error(__('messages.quantityNumber'));
                }
            }
        }
        else {
            foreach ($cost_per_item as $rate) {

                if (is_null($rate)) {
                    return Reply::error(__('purchase::messages.inventory.itemValueBlank'));
                }

                if (!is_numeric($rate)) {
                    return Reply::error(__('messages.unitPriceNumber'));
                }
            }
        }

        $products = $request->product_id;

        DB::beginTransaction();

        foreach ($products as $key => $product) {
            $invoicedItem = InvoiceItems::whereHas('invoice', function ($invoiceQuery) {
                $invoiceQuery->whereIn('status', ['paid', 'partial', 'unpaid']);
            })->where('product_id', $product)->sum('quantity');

            if (!empty($quantity)) {
                if ($quantity[$key] < $invoicedItem) {
                    return Reply::error(__('purchase::messages.inventory.quantityCannotLessThan') . '(' . $invoicedItem . ')');
                }
            }

            $addStock = PurchaseStockAdjustment::where('product_id', $product)->first();

            if (!$addStock) {
                $addStock = new PurchaseStockAdjustment();

                if ($key == 0) {
                    $inventory = new PurchaseInventory();
                }
            }
            else {
                $inventory = PurchaseInventory::where('id', $addStock->inventory_id)->first();
            }

            if ($key == 0) {
                $inventory->date = $request->date ? Carbon::parse($request->date)->format('Y-m-d') : null;
                $inventory->type = $request->type;
                $inventory->reason_id = $request->reason_id;
                $inventory->save();
            }

            $addStock->type = $request->type ?: null;
            $addStock->date = $request->date ? Carbon::parse($request->date)->format('Y-m-d') : null;
            $addStock->inventory_id = $inventory->id;
            $addStock->reason_id = $request->reason_id ?: null;
            $addStock->reference_number = $request->reference_number ?: null;
            $addStock->product_id = $product ?: null;

            if (!empty($quantity)) {
                $addStock->net_quantity = $request->quantity_on_hand[$key] ?: null;
                $addStock->quantity_adjustment = $request->quantity_adjusted[$key] ?: 0;
            }
            else {
                $addStock->changed_value = $request->changed_value[$key] ?: null;
                $addStock->adjusted_value = $request->adjusted_value[$key] ?: 0;
            }

            $addStock->description = $request->description ?: null;
            $addStock->status = $request->formType == 'save' ? 'converted' : 'draft';
            $addStock->save();

            $product = PurchaseProduct::findOrFail($product);

            if (!is_null($addStock->changed_value)) {
                $product->price = $addStock->changed_value;
            }

            $product->save();
        }

        $company = company();

        event(new PurchaseInventoryEvent($inventory, $products, $company));
        DB::commit();


        $defaultImage = $request->default_image ? $request->default_image : '';

        return Reply::successWithData(__('messages.recordSaved'), ['inventoyID' => $inventory->id, 'defaultImage' => $defaultImage]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $this->addPermission = user()->permission('add_inventory');
        $this->viewPermission = user()->permission('view_inventory');
        $this->editPermission = user()->permission('edit_inventory');
        $this->deletePermission = user()->permission('delete_inventory');
        $this->manageStockPermission = user()->permission('manage_stock_adjustment');

        abort_403(!(
            $this->viewPermission == 'all'
            || ($this->viewPermission == 'added')
            || ($this->viewPermission == 'owned')
            || ($this->viewPermission == 'both')
            || ($this->manageStockPermission == 'owned')
        ));

        $this->pageTitle = __('purchase::app.inventoryDetails');

        $this->inventory = PurchaseInventory::with(['files', 'reason', 'stocks' => function ($q) {
            $q->with('product');
        }])->findOrFail($id);

        $this->stock = PurchaseStockAdjustment::where('inventory_id', $id)->first();

        $this->view = 'purchase::purchase-inventory.ajax.overview';

        $tab = request('tab');

        switch ($tab) {

        case 'files':
            $this->view = 'purchase::purchase-inventory.ajax.files';
            break;
        case 'history':
            $this->history = PurchaseInventoryHistory::where('inventory_id', $id)->orderByDesc('id')->get();
            $this->view = 'purchase::purchase-inventory.ajax.history';
            break;
        default:
            $this->view = 'purchase::purchase-inventory.ajax.overview';
            break;
        }

        $this->activeTab = $tab ?: 'overview';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('purchase::purchase-inventory.show', $this->data);
    }

    public function layout(Request $request)
    {
        $this->viewPermission = user()->permission('view_inventory');
        $this->deletePermission = user()->permission('delete_inventory');
        abort_403(!in_array($this->viewPermission, ['all', 'added']));

        $this->inventory = PurchaseInventory::with('files')->findOrFail($request->id);

        $layout = $request->layout == 'listview' ? 'purchase::purchase-inventory.inventory-files.ajax-list' : 'purchase::purchase-inventory.inventory-files.thumbnail-list';

        $view = view($layout, $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'html' => $view]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('purchase::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $inventory = PurchaseInventory::findOrFail($id);

        $this->deletePermission = user()->permission('delete_inventory');
        abort_403(!($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $inventory->added_by == user()->id)));

        $inventory->delete();

        return Reply::successWithData(__('messages.deleteSuccess'), ['redirectUrl' => route('purchase-inventory.index')]);
    }

    public function adjustInventory(Request $request)
    {
        $this->stockAdjustment = PurchaseStockAdjustment::where('product_id', $request->id)->first();
        $companyCurrencyID = company()->currency_id;
        $this->item = PurchaseProduct::with('tax')->findOrFail($request->id);
        $this->invoiceSetting = $this->company->invoiceSetting;
        $exchangeRate = ($request->currencyId) ? Currency::findOrFail($request->currencyId) : Currency::findOrFail($companyCurrencyID);

        if (!is_null($exchangeRate) && !is_null($exchangeRate->exchange_rate)) {

            if ($this->item->total_amount != '') {

                $this->item->price = floor(floor($this->item->total_amount) * $exchangeRate->exchange_rate);

            }
            else {
                /*** @phpstan-ignore-next-line */
                $this->item->price = floor((int)$this->item->price * $exchangeRate->exchange_rate);
            }
        }
        else {
            if ($this->item->total_amount != '') {
                $this->item->price = $this->item->total_amount;
            }
        }

        $this->item->price = number_format((float)$this->item->price, 2, '.', '');
        $this->taxes = Tax::all();

        if ($request->val == 'quantity') {
            $view = view('purchase::purchase-inventory.ajax.add_quantity', $this->data)->render();
        }
        else {
            $view = view('purchase::purchase-inventory.ajax.add_value', $this->data)->render();
        }

        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function addFiles()
    {
        $addPermission = user()->permission('add_inventory');
        abort_403(!in_array($addPermission, ['all', 'added']));

        $this->inventoryId = request()->id;

        return view('purchase::purchase-inventory.inventory-files.create', $this->data);
    }

    public function changeStatus(Request $request)
    {
        $this->editPermission = user()->permission('edit_inventory');
        abort_403(!($this->editPermission == 'all' || ($this->editPermission == 'added' && $this->product->added_by == user()->id)));

        DB::beginTransaction();

        $inventory = PurchaseInventory::findOrFail($request->id);

        $stocks = PurchaseStockAdjustment::where('inventory_id', $inventory->id)->get();

        if ($request->status == 'active') {
            foreach ($stocks as $stock) {
                $stock->update(['status' => 'converted']);
            }
        }
        else {
            foreach ($stocks as $stock) {
                $stock->update(['status' => 'draft']);
            }
        }

        $inventory->status = $request->status;
        $inventory->save();

        DB::commit();

        return Reply::success(__('messages.updateSuccess'));
    }

    public function download($id)
    {
        $this->invoiceSetting = invoice_setting();
        $this->purchaseSetting = PurchaseSetting::first();

        $this->purchaseInventory = PurchaseInventory::findOrFail($id);
        $this->viewPermission = user()->permission('view_inventory');

        abort_403(!(
            $this->viewPermission == 'all'
            || ($this->viewPermission == 'added' && $this->purchaseInventory->added_by == user()->id)
        ));

        App::setLocale($this->invoiceSetting->locale ?? 'en');
        Carbon::setLocale($this->invoiceSetting->locale ?? 'en');

        $pdfOption = $this->domPdfObjectForDownload($id, $this->purchaseInventory);
        $pdf = $pdfOption['pdf'];
        $filename = $pdfOption['fileName'];

        return request()->view ? $pdf->stream($filename . '.pdf') : $pdf->download($filename . '.pdf');
    }

    public function domPdfObjectForDownload($id)
    {
        $this->invoiceSetting = invoice_setting();
        $this->purchaseSetting = PurchaseSetting::first();
        $this->inventory = PurchaseInventory::with('stocks', 'files', 'reason')->findOrFail($id);

        App::setLocale($this->invoiceSetting->locale ?? 'en');
        Carbon::setLocale($this->invoiceSetting->locale ?? 'en');

        $this->company = company();

        $pdf = app('dompdf.wrapper');
        $pdf->setOption('enable_php', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        if ($this->invoiceSetting->template == 'invoice-1') {
            $pdf->loadView('purchase::purchase-inventory.pdf.invoice-1', $this->data);
        }
        else {
            $pdf->loadView('purchase::purchase-inventory.pdf.invoice-5', $this->data);
        }

        $filename = 'inventory-' . $this->inventory->id;

        return [
            'pdf' => $pdf,
            'fileName' => $filename
        ];
    }

}
