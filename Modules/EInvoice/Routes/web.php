<?php



use Illuminate\Support\Facades\Route;
use Modules\EInvoice\Http\Controllers\EInvoiceController;

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


// Admin routes
Route::group(['middleware' => 'auth', 'prefix' => 'account'], function () {
    Route::group(
        ['prefix' => 'settings'],
        function () {
            Route::get('einvoice', [EInvoiceController::class, 'settings'])->name('einvoice.settings');
            Route::get('einvoice-modal', [EInvoiceController::class, 'settingsModal'])->name('einvoice.settings_modal');
        }
    );
    Route::group(
        ['prefix' => 'einvoice', 'as' => 'einvoice.'],
        function () {
            Route::get('/', [EInvoiceController::class, 'index'])->name('index');
            Route::get('/export-xml/{id}', [EInvoiceController::class, 'exportXml'])->name('exportXml');
            Route::put('einvoice-save', [EInvoiceController::class, 'saveSettings'])->name('settings.save');
            Route::get('einvoice-client-modal/{id}', [EInvoiceController::class, 'clientModal'])->name('client_modal');
            Route::put('einvoice-client-save/{id}', [EInvoiceController::class, 'clientSave'])->name('client_save');
        }
    );
});
