<?php

use Modules\Purchase\Entities\PurchaseSetting;

if (!function_exists('purchase_setting')) {
    // @codingStandardsIgnoreLine
    function purchase_setting()
    {
        if (!session()->has('purchase_setting')) {
            return session(['purchase_setting' => PurchaseSetting::first()]);
        }
        return session('purchase_setting');
    }
    // @codingStandardsIgnoreLine
}
