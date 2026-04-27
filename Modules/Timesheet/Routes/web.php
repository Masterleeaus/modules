<?php

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

use Illuminate\Support\Facades\Route;
use Modules\Timesheet\Http\Controllers\TimesheetController;

Route::group(['middleware' => 'PlanModuleCheck:Timesheet'], function ()
{
    Route::resource('timesheet', TimesheetController::class)->middleware(['auth']);
});
Route::get('/totalhours', [TimesheetController::class,'totalhours'])->name('totalhours');
Route::get('gethours/{id}', [TimesheetController::class,'gethours'])->name('gethours');
Route::post('/gettask', [TimesheetController::class,'gettask'])->name('gettask');
