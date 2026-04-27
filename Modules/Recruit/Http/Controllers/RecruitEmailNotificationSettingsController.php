<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use DebugBar\DataCollector\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Modules\Recruit\Entities\RecruitEmailNotificationSetting;
use Modules\Recruit\Entities\RecruitSetting;

class RecruitEmailNotificationSettingsController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->activeSettingMenu = 'recruit_settings';
        $this->middleware(function ($request, $next) {
            abort_403(! in_array(RecruitSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function update(Request $request)
    {
        RecruitEmailNotificationSetting::where('send_email', 'yes')->update(['send_email' => 'no']);

        if ($request->send_email) {
            RecruitEmailNotificationSetting::whereIn('id', $request->send_email)->update(['send_email' => 'yes']);
        }

        $settings = RecruitSetting::where('company_id', company()->id)->first();
        $arr = $request->checkBoardColumn;
        $mailSetting = [];

        foreach ($settings->mail_setting as $id => $setting) {
            $setting['status'] = false;

            if ($request->has('checkBoardColumn') && in_array($id, $arr)) {
                $setting['status'] = true;
            }

            $mailSetting = Arr::add($mailSetting, $id, $setting);
        }

        $settings->mail_setting = $mailSetting;
        $settings->save();


        return Reply::success(__('messages.updateSuccess'));
    }
}
