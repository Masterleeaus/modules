<?php



use Illuminate\Support\Facades\Route;
use Modules\Purchase\Http\Controllers\VendorNotesController;
use Modules\Purchase\Http\Controllers\VendorCreditController;
use Modules\Purchase\Http\Controllers\PurchaseOrderReportController;
use Modules\Purchase\Http\Controllers\ReportsController;
use Modules\Purchase\Http\Controllers\PurchaseInventoryController;
use Modules\Purchase\Http\Controllers\PurchaseInventoryFileController;
use Modules\Purchase\Http\Controllers\PurchaseBillController;
use Modules\Purchase\Http\Controllers\PurchaseOrderController;
use Modules\Purchase\Http\Controllers\PurchaseVendorController;
use Modules\Purchase\Http\Controllers\PurchaseVendorCategoryController;
use Modules\Purchase\Http\Controllers\PurchaseContactController;
use Modules\Purchase\Http\Controllers\PurchaseProductController;
use Modules\Purchase\Http\Controllers\PurchaseSettingController;
use Modules\Purchase\Http\Controllers\PurchaseOrderFileController;
use Modules\Purchase\Http\Controllers\PurchaseSmtpSettingController;
use Modules\Purchase\Http\Controllers\PurchaseVendorPaymentController;
use Modules\Purchase\Http\Controllers\StockAdjustmentReasonController;
use Modules\Purchase\Observers\PurchaseVendorPaymentObserver;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'auth', 'prefix' => 'account'], function () {

    /* Products */
    Route::get('purchase-products/layout', [PurchaseProductController::class, 'layout'])->name('purchase_products.layout');
    Route::get('purchase-products/add-images', [PurchaseProductController::class, 'addImages'])->name('purchase_products.add_images');
    Route::post('purchase-products/store-images', [PurchaseProductController::class, 'storeImages'])->name('purchase_products.store_images');
    Route::post('purchase-products/change-status', [PurchaseProductController::class, 'changeStatus'])->name('purchase_products.change_status');
    Route::get('purchase-products/adjust-inventory', [PurchaseProductController::class, 'adjustInventory'])->name('purchase_products.adjust_inventory');
    Route::post('purchase-products/update-inventory', [PurchaseProductController::class, 'updateInventory'])->name('purchase_products.update_inventory');
    Route::post('purchase-products/apply-quick-action', [PurchaseProductController::class, 'applyQuickAction'])->name('purchase_products.apply_quick_action');
    Route::get('purchase-products/options', [PurchaseProductController::class, 'allPurchaseProductOption'])->name('purchase_products.options');
    Route::resource('purchase-products', PurchaseProductController::class);

    /* Inventory Adjustment Reasons */
    Route::resource('adjustment-reasons', StockAdjustmentReasonController::class);

    // Vendor Payment
    Route::group(
        ['prefix' => 'vendor-payments'],
        function () {

            Route::post('apply-quick-action', [PurchaseVendorPaymentController::class, 'applyQuickAction'])->name('vendor-payments.apply_quick_action');
        }
    );
    Route::get('fetch-bills/{id?}', [PurchaseVendorPaymentController::class, 'fetchBills'])->name('vendor-payments-fetch.fetch_bill');
    Route::resource('vendor-payments', PurchaseVendorPaymentController::class);
    Route::get('vendor-payments/download/{id}', [PurchaseVendorPaymentController::class, 'download'])->name('vendor-payments.download');
    Route::get('vendor-payments/clearAmount', [PurchaseVendorPaymentController::class, 'clearAmount'])->name('vendor-payments.clearAmount');

    /* Contacts */
    Route::resource('purchase-contacts', PurchaseContactController::class);
    Route::post('purchase-contacts/apply-quick-action', [PurchaseContactController::class, 'applyQuickAction'])->name('purchase-contacts.apply_quick_action');

    /*Vendor Credits */
    Route::get('vendor-credits/add-item', [VendorCreditController::class, 'addItem'])->name('vendor-credits.add_item');
    Route::post('vendor-credits/apply-quick-action', [VendorCreditController::class, 'applyQuickAction'])->name('vendor-credits.apply_quick_action');
    Route::get('vendor-credits/download/{id}', [VendorCreditController::class, 'download'])->name('vendor-credits.download');
    Route::get('vendor-credits/getbiils/{id}', [VendorCreditController::class, 'getBills'])->name('vendor-credits.get_bills');
    Route::get('vendor-credits/delete-image', [VendorCreditController::class, 'deleteCreditItemImage'])->name('vendor-credits.delete-image');
    Route::get('vendor-credits/add-bill-item', [VendorCreditController::class, 'addBillItem'])->name('vendor-credits.add_bill_item');
    Route::get('vendor-credits/apply-to-bill/{id}', [VendorCreditController::class, 'applyToBill'])->name('vendor-credits.apply_to_bill');
    Route::get('vendor-credits/create/{id}', [VendorCreditController::class, 'create'])->name('vendor-credits.creates');
    Route::post('vendor-credits/apply-bill-credit/{id}', [VendorCreditController::class, 'applyBillCredit'])->name('vendor-credits.apply_bill_credit');
    Route::resource('vendor-credits', VendorCreditController::class);

    /* Inventory */
    Route::get('purchase-inventory/download/{id}', [PurchaseInventoryController::class, 'download'])->name('purchase_inventory.download');
    Route::get('purchase-inventory/layout', [PurchaseInventoryController::class, 'layout'])->name('purchase_inventory.layout');
    Route::get('purchase-inventory/add-files', [PurchaseInventoryController::class, 'addFiles'])->name('purchase_inventory.add_files');
    Route::post('purchase-inventory/change-status', [PurchaseInventoryController::class, 'changeStatus'])->name('purchase_inventory.change_status');
    Route::get('purchase-inventory/adjust-inventory', [PurchaseInventoryController::class, 'adjustInventory'])->name('purchase_inventory.adjust_inventory');
    Route::resource('purchase-inventory', PurchaseInventoryController::class);

    /* Inventory files */
    Route::get('inventory-files/download/{id}', [PurchaseInventoryFileController::class, 'download'])->name('inventory-files.download');
    Route::resource('inventory-files', PurchaseInventoryFileController::class);

    // Purchase Settings
    Route::resource('purchase-settings', PurchaseSettingController::class);
    Route::post('purchase-settings/update-prefix/{id}', [PurchaseSettingController::class, 'updatePrefix'])->name('purchase_settings.update_prefix');
    Route::resource('purchase-smtp-settings', PurchaseSmtpSettingController::class);

    /* Bills */
    Route::post('bills/send-bill/{billId}', [PurchaseBillController::class, 'sendBill'])->name('bills.send_invoice');
    Route::get('bills/download/{id}', [PurchaseBillController::class, 'download'])->name('bills.download');
    Route::resource('bills', PurchaseBillController::class);

    /* Vendors */
    Route::post('vendors/apply-quick-action', [PurchaseVendorController::class, 'applyQuickAction'])->name('vendors.apply_quick_action');
    Route::get('purchase-order-products', [PurchaseBillController::class, 'purchaseOrderProducts'])->name('purchase_order_products');
    Route::get('purchase-orders', [PurchaseBillController::class, 'vendorPurchaseOrders'])->name('purchase_orders');
    Route::resource('vendors', PurchaseVendorController ::class);

    /* Vendor category */
    Route::resource('vendor-cateogory', PurchaseVendorCategoryController ::class);

    /* Purchase Order */
    Route::get('purchase-order/change-status/{id}', [PurchaseOrderController::class, 'changeStatus'])->name('purchase_order.change_status');
    Route::get('purchase-order/add-item', [PurchaseOrderController::class, 'addItem'])->name('purchase_order.add_item');
    Route::get('purchase-order/delete-image', [PurchaseOrderController::class, 'deletePurchaseItemImage'])->name('purchase_order.delete_image');
    Route::post('purchase-order/send-order/{orderID}', [PurchaseOrderController::class, 'sendOrder'])->name('purchase_order.send_order');
    Route::get('purchase-order/download/{id}', [PurchaseOrderController::class, 'download'])->name('purchase_order.download');
    Route::get('purchase-order/vendor-currency', [PurchaseOrderController::class, 'vendorCurrency'])->name('purchase_order.vendor_currency');
    Route::resource('purchase-order', PurchaseOrderController::class);

    /* Purchase Order files */
    Route::get('purchase-order-file/download/{id}', [PurchaseOrderFileController::class, 'download'])->name('purchase_order_file.download');
    Route::resource('purchase-order-file', PurchaseOrderFileController::class);

    /* Vendor Routes */
    Route::resource('vendor-notes', VendorNotesController::class);
    Route::get('vendor-notes/ask-for-password/{id}', [VendorNotesController::class, 'askForPassword'])->name('vendor_notes.ask_for_password');
    Route::post('vendor-notes/check-password', [VendorNotesController::class, 'checkPassword'])->name('vendor_notes.check_password');
    Route::post('vendor-notes/apply-quick-action', [VendorNotesController::class, 'applyQuickAction'])->name('vendor-notes.apply_quick_action');
    Route::post('vendor-notes/showVerified/{id}', [VendorNotesController::class, 'showVerified'])->name('vendor-notes.show_verified');

    /* Reports */
    Route::resource('reports', ReportsController::class);
    Route::resource('order-report', PurchaseOrderReportController::class);

});
