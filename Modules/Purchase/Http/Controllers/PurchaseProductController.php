<?php

namespace Modules\Purchase\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Task;
use App\Helper\Files;
use App\Helper\Reply;
use App\Models\UnitType;
use App\Models\OrderCart;
use App\Scopes\ActiveScope;
use App\Models\InvoiceItems;
use App\Models\ProductFiles;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Response;
use Modules\Purchase\Entities\PurchaseProduct;
use App\Http\Controllers\AccountBaseController;
use App\Models\Product;
use Modules\Purchase\Entities\PurchaseInventory;
use Modules\Purchase\Entities\PurchaseSetting;
use Modules\Purchase\Events\PurchaseInventoryEvent;
use Modules\Purchase\Entities\PurchaseProductHistory;
use Modules\Purchase\Entities\PurchaseStockAdjustment;
use Modules\Purchase\DataTables\PurchaseProductsDataTable;
use Modules\Purchase\DataTables\PurchaseProductTransaction;
use Modules\Purchase\Entities\PurchaseStockAdjustmentReason;
use Modules\Purchase\Http\Requests\Product\StorePurchaseProductRequest;
use Modules\Purchase\Http\Requests\Product\UpdatePurchaseProductRequest;

class PurchaseProductController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.products';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(PurchaseProductsDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_product');
        abort_403(!in_array($viewPermission, ['all', 'added']));

        $productDetails = [];
        $productDetails = OrderCart::all();
        $this->productDetails = $productDetails;

        $this->totalProducts = PurchaseProduct::count();
        $this->cartProductCount = OrderCart::where('client_id', user()->id)->count();

        $this->categories = ProductCategory::all();
        $this->subCategories = ProductSubCategory::all();
        $this->unitTypes = UnitType::all();

        return $dataTable->render('purchase::purchase-products.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $this->pageTitle = __('app.menu.addProducts');
        $this->addPermission = user()->permission('add_product');
        abort_403(!in_array($this->addPermission, ['all', 'added']));

        $this->taxes = Tax::all();
        $this->categories = ProductCategory::all();
        $this->subCategories = ProductSubCategory::all();
        $productId = request()['duplicate_product'];

        $this->product = $productId ? PurchaseProduct::findOrFail($productId) : null;
        $this->subCategories = ($this->product && !is_null($this->product->sub_category_id)) ? ProductSubCategory::where('category_id', $this->product->category_id)->get() : [];

        $product = new Product();

        if ($product->getCustomFieldGroupsWithFields()) {
            $this->fields = $product->getCustomFieldGroupsWithFields()->fields;
        }

        $this->unit_types = UnitType::all();

        if (request()->ajax()) {
            $html = view('purchase::purchase-products.ajax.create', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'purchase::purchase-products.ajax.create';

        return view('purchase::purchase-products.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(StorePurchaseProductRequest $request)
    {
        $this->addPermission = user()->permission('add_product');
        abort_403(!in_array($this->addPermission, ['all', 'added']));

        $product = new PurchaseProduct();
        $product->name = $request->name;
        $product->taxes = $request->tax ? json_encode($request->tax) : null;
        $product->hsn_sac_code = $request->hsn_sac_code;
        $product->unit_id = $request->unit_type;
        $product->description = trim_editor($request->description);
        $product->allow_purchase = $request->purchase_allow == 'no';
        $product->downloadable = $request->downloadable == 'true';
        $product->category_id = ($request->category_id) ?: null;
        $product->sub_category_id = ($request->sub_category_id) ?: null;
        $product->sku = $request->sku;
        $product->type = $request->type;
        $product->price = $request->selling_price ?? null;

        if (!is_null($request->purchase_information)) {
            $product->purchase_information = $request->purchase_information;
            $product->purchase_price = ($request->purchase_price) ?: null;
        }
        else {
            $product->purchase_information = '0';
            $product->purchase_price = null;
            $product->purchase_description = null;
        }

        if (!is_null($request->track_inventory)) {
            $product->track_inventory = $request->track_inventory;
            $product->opening_stock = $request->opening_stock ?: null;
        }
        else {
            $product->track_inventory = '0';
            $product->opening_stock = null;
        }

        if (request()->hasFile('downloadable_file') && request()->downloadable == 'true') {
            Files::deleteFile($product->downloadable_file, ProductFiles::FILE_PATH);
            $product->downloadable_file = Files::uploadLocalOrS3(request()->downloadable_file, ProductFiles::FILE_PATH);
        }

        $product->save();

        if (!is_null($request->track_inventory)) {
            $addStock = PurchaseStockAdjustment::where('product_id', $product->id)->first();

            if (!$addStock) {
                $inventory = new PurchaseInventory();

                $addStock = new PurchaseStockAdjustment();
                $addStock->product_id = $product->id;
            }
            else {
                $inventory = PurchaseInventory::where('id', $addStock->inventory_id)->first();
            }

            $inventory->date = Carbon::today()->format('Y-m-d');
            $inventory->type = (!is_null($request->opening_stock)) ? 'quantity' : 'value';
            $inventory->reason_id = null;
            $inventory->save();

            $addStock->inventory_id = $inventory->id;
            $addStock->reason_id = null;
            $addStock->date = Carbon::today()->format('Y-m-d');
            $addStock->type = (!is_null($request->opening_stock)) ? 'quantity' : 'value';
            $addStock->net_quantity = $request->opening_stock ?: null;
            $addStock->changed_value = $request->rate_per_unit ?: null;
            $addStock->status = 'converted';
            $addStock->save();
        }

        // To add custom fields data
        if ($request->custom_fields_data) {
            $productData = Product::find($product->id);
            $productData->updateCustomFieldData($request->custom_fields_data);
        }

        $redirectUrl = urldecode($request->redirect_url);

        if ($redirectUrl == '') {
            $redirectUrl = route('purchase-products.index');
        }

        if ($request->add_more == 'true') {
            $html = $this->create();

            return Reply::successWithData(__('messages.recordSaved'), ['html' => $html, 'add_more' => true, 'productID' => $product->id, 'defaultImage' => $request->default_image ?? 0]);
        }

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => $redirectUrl, 'productID' => $product->id, 'defaultImage' => $request->default_image ?? 0]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $this->addPermission = user()->permission('add_product');
        $this->viewPermission = user()->permission('view_product');
        $this->deletePermission = user()->permission('delete_product');
        $this->editInventoryPermission = user()->permission('edit_product');
        abort_403(!($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $this->product->added_by == user()->id)));

        $this->product = PurchaseProduct::with(['category', 'subCategory'])->findOrFail($id);
        $this->inventory = PurchaseStockAdjustment::where('product_id', $id)->first();
        $this->taxes = Tax::withTrashed()->get();
        $this->pageTitle = $this->product->name;

        $this->taxValue = '';
        $taxes = [];

        foreach ($this->taxes as $tax) {
            if ($this->product && isset($this->product->taxes) && json_decode($this->product->taxes) && array_search($tax->id, json_decode($this->product->taxes)) !== false) {
                $taxes[] = $tax->tax_name . ' : ' . $tax->rate_percent . '%';
            }
        }

        $this->taxValue = implode(', ', $taxes);

        $this->task = Task::first();

        $this->productData = Product::find($id)->withCustomFields();

        $getCustomFieldGroupsWithFields = $this->productData->getCustomFieldGroupsWithFields();

        if ($getCustomFieldGroupsWithFields) {
            $this->fields = $getCustomFieldGroupsWithFields->fields;
        }


        $this->view = 'purchase::purchase-products.ajax.overview';

        $tab = request('tab');

        switch ($tab) {

        case 'files':
            $this->view = 'purchase::purchase-products.ajax.files';
            break;
        case 'history':
            $this->history = PurchaseProductHistory::where('purchase_product_id', $id)->orderByDesc('id')->get();
            $this->view = 'purchase::purchase-products.ajax.history';
            break;
        case 'transactions':
            return $this->transactions();
        default:
            $this->view = 'purchase::purchase-products.ajax.overview';
            break;
        }

        $this->commitedStock = InvoiceItems::whereHas('invoice', function ($invoiceQuery) {
            $invoiceQuery->where('status', 'unpaid');
        })->where('product_id', $id)->sum('quantity');

        $this->activeTab = $tab ?: 'overview';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('purchase::purchase-products.show', $this->data);
    }

    public function transactions()
    {
        $this->viewPermission = user()->permission('view_product');
        abort_403(!($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $this->product->added_by == user()->id)));

        $dataTable = new PurchaseProductTransaction();

        $tab = request('tab');
        $this->activeTab = $tab ?: 'transactions';
        $this->view = 'purchase::purchase-products.ajax.transactions';

        return $dataTable->render('purchase::purchase-products.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->product = PurchaseProduct::with('orderItem.purchaseOrder.purchaseBill','quantityInventory')->findOrFail($id);
        foreach ($this->product->orderItem as $orderItem) {
            if ($orderItem->purchaseOrder->purchaseBill) {
                $this->trackInventory = 'disable';
            }
        }

        $invoiceItems = InvoiceItems::where('product_id', $this->product->id)->get();

        if ($invoiceItems->isNotEmpty()) {
            $this->trackInventory = 'disable';
        }

        $this->editPermission = user()->permission('edit_product');
        abort_403(!($this->editPermission == 'all' || ($this->editPermission == 'added' && $this->product->added_by == user()->id)));

        $this->taxes = Tax::all();
        $this->categories = ProductCategory::all();
        $this->unit_types = UnitType::all();
        $this->subCategories = !is_null($this->product->sub_category_id) ? ProductSubCategory::where('category_id', $this->product->category_id)->get() : [];
        $this->pageTitle = __('app.update') . ' ' . __('app.menu.products');

        $images = [];

        if (isset($this->product) && isset($this->product->files)) {
            foreach ($this->product->files as $file) {
                $image['id'] = $file->id;
                $image['name'] = $file->filename;
                $image['hashname'] = $file->hashname;
                $image['size'] = $file->size;
                $image['file_url'] = $file->file_url;
                $images[] = $image;
            }
        }

        $this->images = json_encode($images);

        $this->productData = Product::find($id)->withCustomFields();


        $getCustomFieldGroupsWithFields = $this->productData->getCustomFieldGroupsWithFields();

        if ($getCustomFieldGroupsWithFields) {
            $this->fields = $getCustomFieldGroupsWithFields->fields;
        }


        if (request()->ajax()) {
            $html = view('purchase::purchase-products.ajax.edit', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'purchase::purchase-products.ajax.edit';

        return view('purchase::purchase-products.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(UpdatePurchaseProductRequest $request, $id)
    {
        $product = PurchaseProduct::findOrFail($id);
        $this->editPermission = user()->permission('edit_product');
        abort_403(!($this->editPermission == 'all' || ($this->editPermission == 'added' && $product->added_by == user()->id)));

        $product->name = $request->name;
        $product->taxes = $request->tax ? json_encode($request->tax) : null;
        $product->hsn_sac_code = $request->hsn_sac_code;
        $product->unit_id = $request->unit_type;
        $product->description = trim_editor($request->description);
        $product->allow_purchase = ($request->purchase_allow == 'no') ? true : false;
        $product->downloadable = ($request->downloadable == 'true') ? true : false;
        $product->category_id = ($request->category_id) ? $request->category_id : null;
        $product->sub_category_id = ($request->sub_category_id) ? $request->sub_category_id : null;
        $product->sku = $request->sku;
        $product->type = $request->type;
        $product->price = ($request->selling_price) ?: null;

        if (!is_null($request->purchase_information)) {
            $product->purchase_information = $request->purchase_information;
            $product->purchase_price = ($request->purchase_price) ?: null;
        }
        else {
            $product->purchase_information = 0;
            $product->purchase_price = null;
            $product->purchase_description = null;
        }

        if (!is_null($request->track_inventory)) {
            $product->track_inventory = $request->track_inventory;
            $product->opening_stock = $request->opening_stock ?: null;
        }
        else {
            $product->track_inventory = '0';
            $product->opening_stock = null;
        }

        if (request()->hasFile('downloadable_file') && request()->downloadable == 'true') {
            Files::deleteFile($product->downloadable_file, ProductFiles::FILE_PATH);
            $product->downloadable_file = Files::uploadLocalOrS3(request()->downloadable_file, ProductFiles::FILE_PATH);
        }
        elseif (request()->downloadable == 'true' && $product->downloadable_file == null) {
            $product->downloadable = false;
        }

        if (!request()->hasFile('file')) {
            $product->default_image = request()->default_image;
        }

        $product->save();

        if (!is_null($request->track_inventory)) {
            $addStock = PurchaseStockAdjustment::where('product_id', $product->id)->first();

            if (!$addStock) {
                $inventory = new PurchaseInventory();

                $addStock = new PurchaseStockAdjustment();
                $addStock->product_id = $product->id;
            }
            else {
                $inventory = PurchaseInventory::where('id', $addStock->inventory_id)->first();
            }

            $inventory->date = Carbon::today()->format('Y-m-d');
            $inventory->type = (!is_null($request->opening_stock)) ? 'quantity' : 'value';
            $inventory->reason_id = null;
            $inventory->save();

            $addStock->inventory_id = $inventory->id;
            $addStock->reason_id = null;
            $addStock->date = Carbon::today()->format('Y-m-d');
            $addStock->type = (!is_null($request->opening_stock)) ? 'quantity' : 'value';
            $addStock->net_quantity = $request->opening_stock ?: null;
            $addStock->changed_value = $request->rate_per_unit ?: null;
            $addStock->status = 'converted';
            $addStock->save();
        }

        // To add custom fields data
        if ($request->custom_fields_data) {
            $productData = Product::find($product->id);
            $productData->updateCustomFieldData($request->custom_fields_data);
        }

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('purchase-products.index'), 'productID' => $product->id, 'defaultImage' => $request->default_image ?? 0]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $product = PurchaseProduct::findOrFail($id);
        $this->deletePermission = user()->permission('delete_product');
        abort_403(!($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $product->added_by == user()->id)));

        $stocks = PurchaseStockAdjustment::where('product_id', $product->id)->get();

        foreach ($stocks as $item) {
            $inventory = PurchaseInventory::where('id', $item->inventory_id)->first();
            $inventory->delete();

            $item->delete();
        }

        $product->delete();

        return Reply::successWithData(__('messages.deleteSuccess'), ['redirectUrl' => route('purchase-products.index')]);
    }

    public function storeImages(Request $request)
    {
        if ($request->hasFile('file')) {

            $defaultImage = null;

            foreach ($request->file as $fileData) {
                $file = new ProductFiles();
                $file->product_id = $request->product_id;

                $filename = Files::uploadLocalOrS3($fileData, ProductFiles::FILE_PATH);

                $file->filename = $fileData->getClientOriginalName();
                $file->hashname = $filename;
                $file->size = $fileData->getSize();
                $file->created_at = now();
                $file->save();

                if ($fileData->getClientOriginalName() == $request->default_image) {
                    $defaultImage = $filename;
                }
            }

            if ($request->default_image != 0) {
                $product = PurchaseProduct::findOrFail($request->product_id);
                $product->default_image = $defaultImage;
                $product->save();
            }
        }

        return Reply::success(__('messages.fileUploaded'));
    }

    public function changeStatus(Request $request)
    {
        $this->editPermission = user()->permission('edit_product');
        abort_403(!($this->editPermission == 'all' || ($this->editPermission == 'added' && $this->product->added_by == user()->id)));

        $expense = PurchaseProduct::findOrFail($request->productId);
        $expense->status = $request->status;
        $expense->save();

        return Reply::success(__('messages.updateSuccess'));
    }

    public function applyQuickAction(Request $request)
    {
        switch ($request->action_type) {
        case 'delete':
            $this->deleteRecords($request);

            return Reply::success(__('messages.deleteSuccess'));
        case 'change-status':
            abort_403(user()->permission('edit_product') != 'all');

            PurchaseProduct::withoutGlobalScope(ActiveScope::class)->whereIn('id', explode(',', $request->row_ids))->update(['status' => $request->product_status]);

            return Reply::success(__('messages.updateSuccess'));
        case 'change-purchase':
            abort_403(user()->permission('edit_product') != 'all');

            PurchaseProduct::whereIn('id', explode(',', $request->row_ids))->update(['allow_purchase' => $request->status]);

            return Reply::success(__('messages.updateSuccess'));
        default:
            return Reply::error(__('messages.selectAction'));
        }
    }

    protected function deleteRecords($request)
    {
        abort_403(user()->permission('delete_product') != 'all');

        $products = PurchaseProduct::whereIn('id', explode(',', $request->row_ids))->get();

        foreach ($products as $product) {
            $product->files()->each(function ($file) {
                $file->delete();
            });

            $stocks = PurchaseStockAdjustment::where('product_id', $product->id)->get();

            foreach ($stocks as $item) {
                $inventory = PurchaseInventory::where('id', $item->inventory_id)->first();
                $inventory->delete();

                $item->delete();
            }

            $product->delete();
        }
    }

    public function layout(Request $request)
    {
        $this->viewPermission = user()->permission('view_product');
        $this->deletePermission = user()->permission('delete_product');
        abort_403(!in_array($this->viewPermission, ['all', 'added']));

        $this->product = PurchaseProduct::with('files')->findOrFail($request->id);

        $layout = $request->layout == 'listview' ? 'purchase::purchase-products.product-files.ajax-list' : 'purchase::purchase-products.product-files.thumbnail-list';

        $view = view($layout, $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'html' => $view]);
    }

    public function addImages()
    {
        $addPermission = user()->permission('add_product');
        abort_403(!in_array($addPermission, ['all', 'added']));

        $this->productId = request()->id;

        return view('purchase::purchase-products.product-files.create', $this->data);
    }

    public function adjustInventory()
    {
        $addPermission = user()->permission('add_inventory');
        abort_403(!in_array($addPermission, ['all', 'added']));

        $this->productId = request()->id;
        $this->product = PurchaseProduct::with('unit')->where('id', request()->id)->first();
        $this->adjustment = PurchaseStockAdjustment::where('product_id', request()->id)->first();
        $this->reasons = PurchaseStockAdjustmentReason::all();

        return view('purchase::purchase-products.ajax.update_inventory', $this->data);
    }

    public function updateInventory(Request $request)
    {
        $addPermission = user()->permission('edit_inventory');
        abort_403(!in_array($addPermission, ['all', 'added']));

        $updateStock = PurchaseStockAdjustment::where('product_id', $request->product_id)->first();

        if (!$updateStock) {
            $inventory = new PurchaseInventory();

            $updateStock = new PurchaseStockAdjustment();
            $updateStock->product_id = $request->product_id;
        }
        else {
            $inventory = PurchaseInventory::where('id', $updateStock->inventory_id)->first();
        }

        $inventory->date = Carbon::parse($request->date)->format('Y-m-d');
        $inventory->type = $request->type;
        $inventory->reason_id = null;
        $inventory->save();

        $updateStock->inventory_id = $inventory->id;
        $updateStock->reason_id = $request->reason_id;
        $updateStock->date = Carbon::parse($request->date)->format('Y-m-d');
        $updateStock->reference_number = $request->reference_number;
        $updateStock->type = $request->type;
        $updateStock->description = $request->description;

        if ($request->type == 'quantity') {
            $updateStock->net_quantity = $request->quantity_on_hand;
            $updateStock->quantity_adjustment = $request->quantity_adjusted;
            $updateStock->changed_value = $request->cost_price;
        }
        else {
            $updateStock->changed_value = $request->changed_value;
            $updateStock->adjusted_value = $request->adjusted_value;
        }

        $updateStock->status = 'converted';
        $updateStock->save();

        $product = PurchaseProduct::findOrFail($request->product_id);
        $product->purchase_price = $updateStock->changed_value;
        $product->save();

        $productID = ($request->product_id);

        $products = explode(',', $productID);

        $company = company();

        event(new PurchaseInventoryEvent($inventory, $products, $company));

        return Reply::success(__('messages.recordSaved'));
    }

    public function allPurchaseProductOption()
    {
        if (module_enabled('Purchase')){
            $products = Product::with('inventory')->get();
        }
        else
        {
            $products = Product::all();
        }

        $option = '';

        foreach ($products as $item) {
            if ((!empty($item->inventory) && count($item->inventory) > 0 && $item->inventory[0]) || ($item->type == 'service'))
            {
                if (($item->track_inventory == 1 && $item->inventory[0]->net_quantity > 0) || ($item->type == 'service'))
                    {
                        $option .= '<option data-content="' . $item->name . '" value="' . $item->id . '"> ' . $item->name . '</option>';
                    }
            }
        }

        return Reply::dataOnly(['products' => $option]);
    }

}
