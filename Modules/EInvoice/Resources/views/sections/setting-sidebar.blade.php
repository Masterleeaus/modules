@if(user()->permission('manage_finance_setting') == 'all' && (in_array('invoices', user_modules())))
    <x-setting-menu-item :active="$activeMenu" menu="einvoice_settings" :href="route('einvoice.settings')"
                            :text="__('einvoice::app.menu.einvoiceSettings')"/>
@endif
