<?php

use Illuminate\Support\Facades\Route;
use Modules\ProShots\Http\Controllers\ProShotsController;
use Modules\ProShots\Http\Controllers\ProShotsSettingController;

/*
|--------------------------------------------------------------------------
| Web Routes – ProShots
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'auth', 'prefix' => 'account', 'as' => 'proshots.'], function () {

    // User-facing: professional product photography studio
    Route::get('pro-shots', [ProShotsController::class, 'index'])->name('index');
    Route::post('pro-shots', [ProShotsController::class, 'store'])->name('store');
    Route::delete('pro-shots/{id}', [ProShotsController::class, 'destroy'])->name('destroy');

    // Admin: Pebblely API key settings
    Route::get('admin/pro-shots/settings', [ProShotsSettingController::class, 'index'])->name('settings.index');
    Route::post('admin/pro-shots/settings', [ProShotsSettingController::class, 'update'])->name('settings.update');

    // Job Batches
    Route::get('pro-shots/batches', [\Modules\ProShots\Http\Controllers\JobBatchController::class, 'index'])->name('batches.index');
    Route::post('pro-shots/batches', [\Modules\ProShots\Http\Controllers\JobBatchController::class, 'store'])->name('batches.store');
    Route::get('pro-shots/batches/{batch}', [\Modules\ProShots\Http\Controllers\JobBatchController::class, 'show'])->name('batches.show');
    Route::post('pro-shots/batches/{batch}/publish', function (\Modules\ProShots\Entities\JobBatch $batch) {
        app(\Modules\ProShots\Services\ProShotsCleaningService::class)->publishBatchToVault($batch);
        return redirect()->route('proshots.batches.show', $batch->id)->with(['message' => 'Published to vault.', 'type' => 'success']);
    })->name('batches.publish');
});
