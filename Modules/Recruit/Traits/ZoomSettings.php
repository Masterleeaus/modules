<?php

namespace Modules\Recruit\Traits;

use Illuminate\Support\Facades\Config;
use Modules\Zoom\Entities\ZoomSetting as EntitiesZoomSetting;

trait ZoomSettings
{
    public function setZoomConfigs()
    {
        if (module_enabled('Zoom')) {
            $settings = EntitiesZoomSetting::first();
            $key = ($settings->api_key) ?: env('ZOOM_CLIENT_ID');
            $apiSecret = ($settings->secret_key) ?: env('ZOOM_CLIENT_SECRET');
            $accountId = ($settings->account_id) ?: env('ZOOM_ACCOUNT_ID');

            Config::set('zoom.client_id', $key);
            Config::set('zoom.client_secret', $apiSecret);
            Config::set('zoom.account_id', $accountId);
        }
    }
}
