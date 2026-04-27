@php
$viewPurchaseSettingPermission = user()->permission('view_purchase_setting');
@endphp

@if ((!in_array('client', user_roles())) && $viewPurchaseSettingPermission == 'all' && in_array(\Modules\Purchase\Entities\PurchaseSetting::MODULE_NAME, user_modules()))
<x-setting-menu-item :active="$activeMenu" menu="purchase_settings" :href="route('purchase-settings.index')"
:text="__('purchase::app.menu.purchaseSettings')"/>
@endif
