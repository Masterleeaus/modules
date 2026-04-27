<?php

$addOnOf = 'worksuite-saas-new';

return [
    'name' => 'Purchase',
    'verification_required' => true,
    'envato_item_id' => 48911118,
    'parent_envato_id' => 23263417,
    'parent_min_version' => '5.3.3',
    'script_name' => $addOnOf . '-purchase-module',
    'parent_product_name' => $addOnOf,
    'setting' => \Modules\Purchase\Entities\PurchaseManagementSetting::class,
];
