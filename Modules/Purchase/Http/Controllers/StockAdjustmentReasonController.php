<?php

namespace Modules\Purchase\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Models\BaseModel;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Purchase\Entities\PurchaseSetting;
use Modules\Purchase\Entities\PurchaseStockAdjustmentReason;

class StockAdjustmentReasonController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'purchase::app.adjustStock';
        $this->middleware(function ($request, $next) {

            in_array('client', user_roles()) ? abort_403(!(in_array('orders', $this->user->modules) && user()->permission('add_order') == 'all')) : abort_403(!in_array('products', $this->user->modules));

            return $next($request);
        });

    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $this->addPermission = user()->permission('manage_project_category');
        abort_403(!in_array($this->addPermission, ['all', 'added']));

        $this->reasons = PurchaseStockAdjustmentReason::all();

        return view('purchase::purchase-products.reasons.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $this->addPermission = user()->permission('manage_project_category');
        abort_403(!in_array($this->addPermission, ['all', 'added']));

        $reason = new PurchaseStockAdjustmentReason();
        $reason->name = $request->reason_name;
        $reason->save();

        $reasons = PurchaseStockAdjustmentReason::all();
        $options = BaseModel::options($reasons, $reason, 'name');

        return Reply::successWithData(__('messages.recordSaved'), ['data' => $options]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return array
     */
    public function update(Request $request, $id)
    {
        $reason = PurchaseStockAdjustmentReason::findOrFail($id);
        $reason->name = strip_tags($request->reason_name);
        $reason->save();

        $reasons = PurchaseStockAdjustmentReason::all();
        $options = BaseModel::options($reasons, null, 'name');

        return Reply::successWithData(__('messages.updateSuccess'), ['data' => $options]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return array
     */
    public function destroy($id)
    {
        PurchaseStockAdjustmentReason::destroy($id);
        $reasons = PurchaseStockAdjustmentReason::all();
        $options = BaseModel::options($reasons, null, 'name');

        return Reply::successWithData(__('messages.deleteSuccess'), ['data' => $options]);
    }

}
