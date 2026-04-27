<?php
use Modules\WorksuiteWorkOrders\Http\Controllers\SettingsController;

use Illuminate\Support\Facades\Route;
use Modules\WorksuiteWorkOrders\Http\Controllers\WorkOrderController;
use Modules\WorksuiteWorkOrders\Http\Controllers\WOTypeController;
use Modules\WorksuiteWorkOrders\Http\Controllers\WORequestController;
use Modules\WorksuiteWorkOrders\Http\Controllers\WOServiceAppointmentController;
use Modules\WorksuiteWorkOrders\Http\Controllers\WOServiceTaskController;
use Modules\WorksuiteWorkOrders\Http\Controllers\WOServicePartController;
use Modules\WorksuiteWorkOrders\Http\Controllers\ServiceTaskController;
use Modules\WorksuiteWorkOrders\Http\Controllers\ServicePartController;

Route::middleware(['web','auth'])
    ->prefix('workorders')
    ->as('workorders.')
    ->group(function () {
        Route::view('/', 'workorders::index')->name('index')->middleware('permission:workorders.view');

        Route::post('orders/{id}/convert-to-project', [WorkOrderController::class, 'convertToProject'])
            ->middleware('permission:workorders.update')
            ->name('orders.convert');
            Route::resource('orders', WorkOrderController::class)->middleware([
            'index'   => 'permission:workorders.view',
            'show'    => 'permission:workorders.view',
            'create'  => 'permission:workorders.create',
            'store'   => 'permission:workorders.create',
            'edit'    => 'permission:workorders.update',
            'update'  => 'permission:workorders.update',
            'destroy' => 'permission:workorders.delete',
        ]);

        Route::resource('types', WOTypeController::class)->middleware('permission:workorders.types.manage');
        Route::resource('requests', WORequestController::class)->middleware('permission:workorders.requests.manage');
        Route::resource('appointments', WOServiceAppointmentController::class)->middleware('permission:workorders.appointments.manage');
        Route::resource('tasks', WOServiceTaskController::class)->middleware('permission:workorders.tasks.manage');
        Route::resource('parts', WOServicePartController::class)->middleware('permission:workorders.parts.manage');
        Route::resource('service-tasks', ServiceTaskController::class)->middleware('permission:workorders.tasks.manage');
        Route::resource('service-parts', ServicePartController::class)->middleware('permission:workorders.parts.manage');
    });


Route::middleware(['web','can:workorders.settings'])->prefix('admin/workorders')->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('workorders.settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('workorders.settings.update');
});
