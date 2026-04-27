<?php



use Illuminate\Support\Facades\Route;
use Modules\Biometric\Http\Controllers\BiometricAttendanceController;
use Modules\Biometric\Http\Controllers\BiometricDeviceController;
use Modules\Biometric\Http\Controllers\BiometricEmployeeController;

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


    Route::post('biometric-devices/change-status', [BiometricDeviceController::class, 'changeStatus'])->name('biometric-devices.change-status');
    Route::post('biometric-devices/sync-employees', [BiometricDeviceController::class, 'syncEmployees'])->name('biometric-devices.sync-employees');
    Route::get('biometric-employees/get-employees-to-sync', [BiometricEmployeeController::class, 'getEmployeesToSync'])->name('biometric-employees.get-employees-to-sync');
    Route::delete('biometric-employees/{id}/remove-from-device', [BiometricEmployeeController::class, 'removeFromDevice'])->name('biometric-employees.remove-from-device');
    Route::get('biometric-commands', [BiometricDeviceController::class, 'commands'])->name('biometric-devices.commands');
    Route::get('biometric-employees/get-info/{id}', [BiometricEmployeeController::class, 'getEmployeeInfo'])->name('biometric-employees.get-info');
    Route::get('get-biometric-attendance', [BiometricAttendanceController::class, 'index'])->name('get-biometric-attendance');
    Route::resource('biometric-devices', BiometricDeviceController::class);
    Route::resource('biometric-employees', BiometricEmployeeController::class)->except(['show']);

    Route::get('biometric-employees/fetch-biometric-data/{id?}', [BiometricEmployeeController::class, 'getEmployeeInfo'])->name('biometric-employees.fetch-biometric-data');
    Route::get('biometric-employees/fetch-all', [BiometricEmployeeController::class, 'fetchAll'])->name('biometric-employees.fetch-all');
});
