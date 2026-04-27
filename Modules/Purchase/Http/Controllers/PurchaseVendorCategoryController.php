<?php

namespace Modules\Purchase\Http\Controllers;

use Carbon\Carbon;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Modules\Purchase\Entities\PurchaseVendorCategory;
use Illuminate\Contracts\Support\Renderable;
use Modules\Purchase\Http\Requests\Vendor\StoreClientCategory;
use Modules\Purchase\Entities\PurchaseSetting;
use App\Http\Controllers\AccountBaseController;

class PurchaseVendorCategoryController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'purchase::app.menu.PurchaseVendorCategoryController';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $this->categories = PurchaseVendorCategory::all();
        $this->deletePermission = user()->permission('manage_client_category');

        return view('purchase::vendors.ajax.create_category', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreClientCategory $request
     * @return array
     */
    public function store(StoreClientCategory $request)
    {
        $category = new PurchaseVendorCategory();
        $category->company_id = company()->id;
        $category->category_name = strip_tags($request->category_name);
        $category->save();
        $categoryData = PurchaseVendorCategory::all();

        return Reply::successWithData(__('messages.recordSaved'), ['data' => $categoryData]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return array|void
     */
    public function update(StoreClientCategory $request, $id)
    {

        $category = PurchaseVendorCategory::findOrFail($id);
        $category->category_name = strip_tags($request->category_name);

        $category->save();

        $categoryData = PurchaseVendorCategory::all();

        return Reply::successWithData(__('messages.updateSuccess'), ['data' => $categoryData]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return mixed
     */
    public function destroy($id)
    {

        $category = PurchaseVendorCategory::findOrFail($id);
        $category->delete();
        $categoryData = PurchaseVendorCategory::all();

        return Reply::successWithData(__('messages.deleteSuccess'), ['data' => $categoryData]);
    }


}
