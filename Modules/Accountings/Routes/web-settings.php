<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'accountings/settings'], function () {
    Route::get('/', 'AccSettingController@index')->name('acc-settings.index');
    Route::post('/', 'AccSettingController@store')->name('acc-settings.store');
});
