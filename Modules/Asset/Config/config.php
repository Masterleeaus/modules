<?php

$addOnOf = 'worksuite-saas-new';
$product = $addOnOf . '-asset-module';

return [
    'name' => 'Asset',
    'verification_required' => true,
    'envato_item_id' => 25830090,
    'parent_envato_id' => 23263417,
    'parent_min_version' => '5.2.5',
    'script_name' => $product,
    'parent_product_name' => $addOnOf,
    'setting' => \Modules\Asset\Entities\AssetSetting::class,
];
