<?php

use Illuminate\Support\Facades\Route;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
 */

Route::middleware('api')->group(function () {

    // Version 1 API Routes - Shared HR APIs for all apps
    Route::prefix('v1')->name('api.v1.')->group(function () {

        // Protected routes (requires JWT authentication)
        Route::middleware(['auth:api'])->group(function () {

            // Attendance routes
            Route::prefix('attendance')->name('attendance.')->group(function () {
                Route::post('check-in', [\Modules\HRCore\app\Http\Controllers\Api\V1\AttendanceController::class, 'checkIn'])->name('check-in');
                Route::post('check-out', [\Modules\HRCore\app\Http\Controllers\Api\V1\AttendanceController::class, 'checkOut'])->name('check-out');
                Route::get('status', [\Modules\HRCore\app\Http\Controllers\Api\V1\AttendanceController::class, 'status'])->name('status');
                Route::get('history', [\Modules\HRCore\app\Http\Controllers\Api\V1\AttendanceController::class, 'history'])->name('history');
                Route::get('logs', [\Modules\HRCore\app\Http\Controllers\Api\V1\AttendanceController::class, 'logs'])->name('logs');
            });

            // Employee Profile routes
            Route::prefix('employee')->name('employee.')->group(function () {
                Route::get('profile', [\Modules\HRCore\app\Http\Controllers\Api\V1\EmployeeController::class, 'profile'])->name('profile');
                Route::put('profile', [\Modules\HRCore\app\Http\Controllers\Api\V1\EmployeeController::class, 'updateProfile'])->name('profile.update');
                Route::post('profile/avatar', [\Modules\HRCore\app\Http\Controllers\Api\V1\EmployeeController::class, 'updateAvatar'])->name('avatar.update');
                Route::get('bank-accounts', [\Modules\HRCore\app\Http\Controllers\Api\V1\EmployeeController::class, 'bankAccounts'])->name('bank-accounts');
                Route::get('history', [\Modules\HRCore\app\Http\Controllers\Api\V1\EmployeeController::class, 'history'])->name('history');
            });

            // Leave routes
            Route::prefix('leaves')->name('leaves.')->group(function () {
                Route::get('balance', [\Modules\HRCore\app\Http\Controllers\Api\V1\LeaveController::class, 'balance'])->name('balance');
                Route::get('types', [\Modules\HRCore\app\Http\Controllers\Api\V1\LeaveController::class, 'types'])->name('types');
                Route::get('history', [\Modules\HRCore\app\Http\Controllers\Api\V1\LeaveController::class, 'history'])->name('history');
                Route::post('apply', [\Modules\HRCore\app\Http\Controllers\Api\V1\LeaveController::class, 'apply'])->name('apply');
                Route::get('team', [\Modules\HRCore\app\Http\Controllers\Api\V1\LeaveController::class, 'teamLeaves'])->name('team');
                Route::get('{id}', [\Modules\HRCore\app\Http\Controllers\Api\V1\LeaveController::class, 'show'])->name('show');
                Route::put('{id}', [\Modules\HRCore\app\Http\Controllers\Api\V1\LeaveController::class, 'update'])->name('update');
                Route::delete('{id}', [\Modules\HRCore\app\Http\Controllers\Api\V1\LeaveController::class, 'cancel'])->name('cancel');
                Route::post('{id}/approve', [\Modules\HRCore\app\Http\Controllers\Api\V1\LeaveController::class, 'approve'])->name('approve');
                Route::post('{id}/reject', [\Modules\HRCore\app\Http\Controllers\Api\V1\LeaveController::class, 'reject'])->name('reject');
                Route::get('holidays/list', [\Modules\HRCore\app\Http\Controllers\Api\V1\LeaveController::class, 'holidays'])->name('holidays');
                Route::post('compensatory-off/apply', [\Modules\HRCore\app\Http\Controllers\Api\V1\LeaveController::class, 'applyCompOff'])->name('comp-off.apply');
                Route::get('compensatory-off/list', [\Modules\HRCore\app\Http\Controllers\Api\V1\LeaveController::class, 'compOffList'])->name('comp-off.list');
            });

            // Expense routes
            Route::prefix('expenses')->name('expenses.')->group(function () {
                Route::get('types', [\Modules\HRCore\app\Http\Controllers\Api\V1\ExpenseController::class, 'getTypes'])->name('types');
                Route::get('summary', [\Modules\HRCore\app\Http\Controllers\Api\V1\ExpenseController::class, 'summary'])->name('summary');
                Route::get('/', [\Modules\HRCore\app\Http\Controllers\Api\V1\ExpenseController::class, 'index'])->name('index');
                Route::post('/', [\Modules\HRCore\app\Http\Controllers\Api\V1\ExpenseController::class, 'store'])->name('store');
                Route::get('{id}', [\Modules\HRCore\app\Http\Controllers\Api\V1\ExpenseController::class, 'show'])->name('show');
                Route::put('{id}', [\Modules\HRCore\app\Http\Controllers\Api\V1\ExpenseController::class, 'update'])->name('update');
                Route::delete('{id}', [\Modules\HRCore\app\Http\Controllers\Api\V1\ExpenseController::class, 'destroy'])->name('destroy');
                Route::post('{id}/upload', [\Modules\HRCore\app\Http\Controllers\Api\V1\ExpenseController::class, 'uploadDocument'])->name('upload');
            });

            // Organization routes
            Route::prefix('organization')->name('organization.')->group(function () {
                Route::get('departments', [\Modules\HRCore\app\Http\Controllers\Api\V1\DepartmentController::class, 'index'])->name('departments');
                Route::get('designations', [\Modules\HRCore\app\Http\Controllers\Api\V1\DesignationController::class, 'index'])->name('designations');
                Route::get('shifts', [\Modules\HRCore\app\Http\Controllers\Api\V1\ShiftController::class, 'index'])->name('shifts');
                Route::get('shifts/my-schedule', [\Modules\HRCore\app\Http\Controllers\Api\V1\ShiftController::class, 'mySchedule'])->name('shifts.my-schedule');
                Route::get('holidays', [\Modules\HRCore\app\Http\Controllers\Api\V1\HolidayController::class, 'index'])->name('holidays');
                Route::get('teams', [\Modules\HRCore\app\Http\Controllers\Api\V1\TeamController::class, 'index'])->name('teams');
                Route::get('teams/{id}/members', [\Modules\HRCore\app\Http\Controllers\Api\V1\TeamController::class, 'members'])->name('teams.members');
            });
        });
    });
});
