<?php

namespace Modules\Purchase\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Modules\Purchase\Entities\PurchaseNotificationSetting;
use Modules\Purchase\Entities\PurchaseSetting;

class PurchaseSmtpSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();

        $this->activeSettingMenu = 'purchase_settings';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    public function update(Request $request, $id)
    {
        PurchaseNotificationSetting::where('send_email', 'yes')->update(['send_email' => 'no']);

        if ($request->send_email) {
            PurchaseNotificationSetting::whereIn('id', $request->send_email)->update(['send_email' => 'yes']);
        }

        return Reply::success(__('messages.updateSuccess'));
    }

}
