<?php

namespace Modules\Purchase\Http\Controllers;

use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use Modules\Purchase\Entities\PurchaseSetting;
use App\Http\Controllers\AccountBaseController;
use Modules\Purchase\Entities\PurchaseNotificationSetting;
use Modules\Purchase\Http\Requests\PurchaseSetting\UpdatePurchaseSettingRequest;

class PurchaseSettingController extends AccountBaseController
{

    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function __construct()
    {
        parent::__construct();

        $this->activeSettingMenu = 'purchase_settings';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    public function index()
    {
        $tab = request('tab');

        $this->pageTitle = 'purchase::app.menu.purchase';
        $this->purchaseSetting = PurchaseSetting::first();
        $this->emailSettings = PurchaseNotificationSetting::all();

        $sendEmailCount = $this->emailSettings->filter(function ($value) {
            return $value->send_email == 'yes';
        })->count();

        $this->checkedAll = $this->emailSettings->count() == $sendEmailCount;

        switch ($tab) {
        case 'purchase-notification-setting':
            $this->view = 'purchase::purchase-settings.ajax.purchase-notification-setting';
            break;
        default:
            $this->purchaseSetting = PurchaseSetting::first();
            $this->view = 'purchase::purchase-settings.ajax.general';
            break;
        }

        $this->activeTab = $tab ?: 'general';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle, 'activeTab' => $this->activeTab]);
        }

        return view('purchase::purchase-settings.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('purchase::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */

    public function show($id)
    {
        return view('purchase::show');
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


    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    public function updatePrefix(UpdatePurchaseSettingRequest $request, $id)
    {
        $purchaseSetting = PurchaseSetting::findOrFail($id);

        $purchaseSetting->purchase_order_prefix = $request->purchase_order_prefix;
        $purchaseSetting->purchase_order_number_separator = $request->purchase_order_number_seprator;
        $purchaseSetting->purchase_order_number_digit = $request->purchase_order_digit;
        $purchaseSetting->bill_prefix = $request->bill_prefix;
        $purchaseSetting->bill_number_separator = $request->bill_number_seprator;
        $purchaseSetting->bill_number_digit = $request->bill_digit;
        $purchaseSetting->vendor_credit_prefix = $request->vendor_credit_prefix;
        $purchaseSetting->vendor_credit_number_seprator = $request->vendor_credit_number_seprator;
        $purchaseSetting->vendor_credit_number_digit = $request->vendor_credit_digit;
        $purchaseSetting->purchase_terms = $request->purchase_terms;

        $purchaseSetting->save();

        cache()->forget('purchase_setting_' . $purchaseSetting->company_id);

        return Reply::success(__('messages.updateSuccess'));

    }

}
