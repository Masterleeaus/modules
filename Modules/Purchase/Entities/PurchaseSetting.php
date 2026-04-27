<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use App\Models\ModuleSetting;
use App\Traits\HasCompany;

class PurchaseSetting extends BaseModel
{

    use HasCompany;

    protected $table = 'purchase_settings';

    const MODULE_NAME = 'purchase';

    public static function addModuleSetting($company)
    {
        self::purchaseSettingInsert($company);
        self::insertNotification($company);
        $roles = ['employee', 'admin'];
        ModuleSetting::createRoleSettingEntry(PurchaseManagementSetting::MODULE_NAME, $roles, $company);
    }

    public static function purchaseSettingInsert($company)
    {
        $setting = new PurchaseSetting();
        $setting->company_id = $company->id;
        $setting->purchase_order_prefix = 'PO';
        $setting->purchase_order_number_separator = '#';
        $setting->purchase_order_number_digit = '3';
        $setting->bill_prefix = 'BL';
        $setting->bill_number_separator = '#';
        $setting->bill_number_digit = '3';
        $setting->vendor_credit_prefix = 'VC';
        $setting->vendor_credit_number_seprator = '#';
        $setting->vendor_credit_number_digit = '3';
        $setting->purchase_code = null;
        $setting->saveQuietly();

    }

    public static function insertNotification($company)
    {
        $notificationSettings = [
            [
                'send_email' => 'yes',
                'setting_name' => 'New Purchase Order',
                'slug' => 'new-purchase-order',
            ],
            [
                'send_email' => 'yes',
                'setting_name' => 'New Purchase Bill',
                'slug' => 'new-purchase-bill',
            ],
            [
                'send_email' => 'yes',
                'setting_name' => 'Admin New Vendor Payment',
                'slug' => 'admin-new-vendor-payment',
            ],
            [
                'send_email' => 'yes',
                'setting_name' => 'Update New Vendor Payment',
                'slug' => 'update-new-vendor-payment',
            ],
            [
                'send_email' => 'yes',
                'setting_name' => 'Vendor Credit',
                'slug' => 'vendor-credit',
            ],
            [
                'send_email' => 'yes',
                'setting_name' => 'New Purchase Inventory',
                'slug' => 'new-purchase-inventory',
            ],
        ];

        foreach ($notificationSettings as $notificationSetting) {
            $notificationSetting['company_id'] = $company->id;
            $notificationSetting = PurchaseNotificationSetting::firstOrNew($notificationSetting);
            $notificationSetting->saveQuietly();
        }
    }

}
