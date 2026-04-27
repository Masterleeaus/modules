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
});
