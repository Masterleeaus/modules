<?php

use Illuminate\Support\Facades\Route;
use Modules\Asset\Http\Controllers\AssetAllocationController;
use Modules\Asset\Http\Controllers\AssetController;
use Modules\Asset\Http\Controllers\AssetHistoryController;
use Modules\Asset\Http\Controllers\AssetMaintenanceController;
use Modules\Asset\Http\Controllers\AssetScanController;
use Modules\Asset\Http\Controllers\AssetSettingController;
use Modules\Asset\Http\Controllers\AssetTypeController;
use Modules\Asset\Http\Controllers\RevokeAllocatedAssetController;

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
    Route::resource('assets', AssetController::class);

    Route::prefix('assets')->group(function () {
        //phpcs:ignore
        Route::get('/asset/{asset}/history/return/{history}', [AssetHistoryController::class, 'returnAsset'])->name('assets.return');
        Route::resource('/asset/{asset}/history', AssetHistoryController::class)->names([
            'create' => 'history.create',
            'store' => 'history.store',
            'edit' => 'history.edit',
            'update' => 'history.update',
            'destroy' => 'history.destroy',
        ]);

        Route::resource('asset-type', AssetTypeController::class);

        // Maintenance schedule routes
        Route::resource('asset-maintenances', AssetMaintenanceController::class);

        // Asset allocation (assignment to cleaner/vehicle)
        Route::resource('asset-allocations', AssetAllocationController::class);
        Route::resource('asset-revocations', RevokeAllocatedAssetController::class);

        // QR scan quick-actions
        Route::get('/scan/{asset}', [AssetScanController::class, 'show'])->name('assets.scan');
        Route::post('/scan/{asset}/issue', [AssetScanController::class, 'issue'])->name('assets.scan.issue');
        Route::post('/scan/{asset}/return', [AssetScanController::class, 'returnAsset'])->name('assets.scan.return');
        Route::post('/scan/{asset}/report', [AssetScanController::class, 'report'])->name('assets.scan.report');
        Route::post('/scan/{asset}/send-to-maintenance', [AssetScanController::class, 'sendToMaintenance'])->name('assets.scan.send-to-maintenance');
        Route::post('/scan/{asset}/complete-maintenance', [AssetScanController::class, 'completeMaintenance'])->name('assets.scan.complete-maintenance');
        Route::post('/scan/{asset}/allocate', [AssetScanController::class, 'allocate'])->name('assets.scan.allocate');
        Route::post('/scan/{asset}/revoke-allocation', [AssetScanController::class, 'revokeAllocation'])->name('assets.scan.revoke-allocation');
    });

    Route::resource('/asset-setting', AssetSettingController::class);
});
