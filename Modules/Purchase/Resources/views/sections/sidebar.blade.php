@php
    $purchaseViewVendorPermission = user()->permission('view_vendor');
    $purchaseViewOrderPermission = user()->permission('view_purchase_order');
    $purchaseViewBillPermission = user()->permission('view_bill');
    $purchaseViewCreditPermission = user()->permission('view_vendor_credit');
    $purchaseViewInventoryPermission = user()->permission('view_inventory');
    $purchaseViewOrderReportPermission = user()->permission('view_order_report');
    $purchaseViewPaymentPermission = user()->permission('view_vendor_payment');
@endphp
@if (in_array(\Modules\Purchase\Entities\PurchaseManagementSetting::MODULE_NAME, user_modules()) && ($purchaseViewVendorPermission != 'none' || $purchaseViewOrderPermission != 'none' || $purchaseViewBillPermission != 'none'
|| $purchaseViewCreditPermission != 'none' || $purchaseViewInventoryPermission != 'none' || $purchaseViewOrderReportPermission != 'none' || $purchaseViewPaymentPermission != 'none'))

    <x-menu-item icon="wallet" :text="__('Materials & Parts')" :addon="App::environment('demo')">
        <x-slot name="iconPath">
            <path d="m14.12 10.163 1.715.858c.22.11.22.424 0 .534L8.267 15.34a.6.6 0 0 1-.534 0L.165 11.555a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0l5.317-2.66zM7.733.063a.6.6 0 0 1 .534 0l7.568 3.784a.3.3 0 0 1 0 .535L8.267 8.165a.6.6 0 0 1-.534 0L.165 4.382a.299.299 0 0 1 0-.535z"/>
            <path d="m14.12 6.576 1.715.858c.22.11.22.424 0 .534l-7.568 3.784a.6.6 0 0 1-.534 0L.165 7.968a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0z"/>
        </x-slot>

        <div class="accordionItemContent pb-2">

            <!-- NAV ITEM - VENDORS -->
            <x-sub-menu-item :link="route('vendors.index')"
                            :text="__('Suppliers')"
                            :permission="($purchaseViewVendorPermission != 'none' && $purchaseViewVendorPermission != '')"
            />

            <!-- NAV ITEM - PRODUCTS -->
            @if (in_array('products', user_modules()) && $sidebarUserPermissions['view_product'] != 5 && $sidebarUserPermissions['view_product'] != 'none')
               <x-sub-menu-item :link="route('purchase-products.index')" :text="__('purchase::app.menu.products')" />
            @endif

            <!-- NAV ITEM - ORDERS -->
            <x-sub-menu-item :link="route('purchase-order.index')"
                            :text="__('purchase::app.menu.purchaseOrder')"
                            :permission="($purchaseViewOrderPermission != 'none' && $purchaseViewOrderPermission != '')"
            />

            <!-- NAV ITEM - BILLS -->
            <x-sub-menu-item :link="route('bills.index')"
                            :text="__('purchase::app.menu.bills')"
                            :permission="($purchaseViewBillPermission != 'none' && $purchaseViewBillPermission != '')"
            />

            <!-- NAV ITEM - PAYMENTS -->
            <x-sub-menu-item :link="route('vendor-payments.index')"
                            :text="__('Supplier Payments')"
                            :permission="($purchaseViewPaymentPermission != 'none' && $purchaseViewPaymentPermission != '')"
            />

            <x-sub-menu-item :link="route('vendor-credits.index')"
                            :text="__('Supplier Credits')"
                            :permission="($purchaseViewCreditPermission != 'none' && $purchaseViewCreditPermission != '')"
            />

            <!-- NAV ITEM - INVENTORY -->
            <x-sub-menu-item :link="route('purchase-inventory.index')" :text="__('purchase::app.menu.inventory')"
            :permission="($purchaseViewInventoryPermission != 'none' && $purchaseViewInventoryPermission != '')"
            />

            <!-- NAV ITEM - REPORTS -->
            <x-sub-menu-item :link="route('reports.index')" :text="__('purchase::app.menu.reports')"
            :permission="($purchaseViewOrderReportPermission != 'none' && $purchaseViewOrderReportPermission != '')"
            />

        </div>

    </x-menu-item>

@endif
