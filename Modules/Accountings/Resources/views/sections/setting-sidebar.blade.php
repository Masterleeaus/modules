@php($pageTitle = $pageTitle ?? __('accountings::app.menu.accounting'))
@if (in_array(\Modules\Accountings\Entities\Accounting::MODULE_NAME, user_modules()) && in_array('admin', user_roles()))
<x-setting-menu-item :active="$activeMenu" menu="accounting_settings" :href="route('acc-settings.index')" :text="__('accountings::modules.acc.accountingSettings')" />
@endif
