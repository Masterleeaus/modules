<?php

use Illuminate\Support\Facades\Route;
use Modules\HRCore\app\Http\Controllers\AttendanceController;
use Modules\HRCore\app\Http\Controllers\AttendanceDashboardController;
use Modules\HRCore\app\Http\Controllers\AttendanceRegularizationController;
use Modules\HRCore\app\Http\Controllers\CompensatoryOffController;
use Modules\HRCore\app\Http\Controllers\DepartmentsController;
use Modules\HRCore\app\Http\Controllers\DesignationController;
use Modules\HRCore\app\Http\Controllers\EmployeeController;
use Modules\HRCore\app\Http\Controllers\ExpenseController;
use Modules\HRCore\app\Http\Controllers\ExpenseTypeController;
use Modules\HRCore\app\Http\Controllers\HolidayController;
use Modules\HRCore\app\Http\Controllers\LeaveController;
use Modules\HRCore\app\Http\Controllers\LeaveTypeController;
use Modules\HRCore\app\Http\Controllers\OrganisationHierarchyController;
use Modules\HRCore\app\Http\Controllers\ReportController;
use Modules\HRCore\app\Http\Controllers\ShiftController;
use Modules\HRCore\app\Http\Controllers\TeamController;

/*
|--------------------------------------------------------------------------
| HRCore Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the HRCore module.
| All routes are prefixed with 'hrcore' and named with 'hrcore.' prefix
| for consistency and to avoid conflicts with other modules.
|
*/

Route::prefix('hrcore')->name('hrcore.')->middleware(['auth', 'web'])->group(function () {

    // Dashboard
    Route::get('/', function () {
        return redirect()->route('hrcore.attendance.index');
    })->name('dashboard');
    Route::get('/dashboard', function () {
        return redirect()->route('hrcore.attendance.index');
    })->name('dashboard.index');

    // Employee Management
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/datatable', [EmployeeController::class, 'indexAjax'])->name('datatable');
        Route::get('/search', [EmployeeController::class, 'search'])->name('search');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/my-profile', [EmployeeController::class, 'myProfile'])->name('my-profile');
        Route::get('/{id}', [EmployeeController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{id}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/update-status', [EmployeeController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/lifecycle/change', [EmployeeController::class, 'changeLifecycleState'])->name('lifecycle.change');
    });

    // ===================================================================
    // EMPLOYEE SELF-SERVICE ROUTES
    // All self-service routes under /my prefix - always use auth()->id()
    // ===================================================================
    Route::prefix('my')->name('my.')->middleware(['self_service'])->group(function () {
        // Profile Management
        Route::get('/profile', [EmployeeController::class, 'selfServiceProfile'])->name('profile');
        Route::post('/profile/update', [EmployeeController::class, 'updateSelfProfile'])->name('profile.update');
        Route::post('/profile/photo', [EmployeeController::class, 'updateProfilePhoto'])->name('profile.photo');
        Route::post('/profile/password', [EmployeeController::class, 'changePassword'])->name('profile.password');

        // My Attendance
        Route::get('/attendance', [AttendanceController::class, 'myAttendance'])->name('attendance');
        Route::get('/reports', [AttendanceController::class, 'myReports'])->name('reports');
        Route::get('/attendance/regularization', [AttendanceRegularizationController::class, 'myRegularizations'])->name('attendance.regularization');
        Route::get('/attendance/regularization/datatable', [AttendanceRegularizationController::class, 'myRegularizationsAjax'])->name('attendance.regularization.datatable');
        Route::post('/attendance/regularization', [AttendanceRegularizationController::class, 'storeMyRegularization'])->name('attendance.regularization.store');
        Route::get('/attendance/regularization/{id}', [AttendanceRegularizationController::class, 'showMyRegularization'])->name('attendance.regularization.show');
        Route::get('/attendance/regularization/{id}/edit', [AttendanceRegularizationController::class, 'editMyRegularization'])->name('attendance.regularization.edit');
        Route::put('/attendance/regularization/{id}', [AttendanceRegularizationController::class, 'updateMyRegularization'])->name('attendance.regularization.update');
        Route::delete('/attendance/regularization/{id}', [AttendanceRegularizationController::class, 'deleteMyRegularization'])->name('attendance.regularization.delete');

        // My Leave Management
        Route::get('/leaves', [LeaveController::class, 'myLeaves'])->name('leaves');
        Route::get('/leaves/datatable', [LeaveController::class, 'myLeavesAjax'])->name('leaves.datatable');
        Route::get('/leaves/balance', [LeaveController::class, 'myBalance'])->name('leaves.balance');
        Route::get('/leaves/apply', [LeaveController::class, 'applyLeave'])->name('leaves.apply');
        Route::post('/leaves/apply', [LeaveController::class, 'storeMyLeave'])->name('leaves.store');
        Route::get('/leaves/{id}', [LeaveController::class, 'showMyLeave'])->name('leaves.show');
        Route::post('/leaves/{id}/cancel', [LeaveController::class, 'cancelMyLeave'])->name('leaves.cancel');

        // My Expenses
        Route::get('/expenses', [ExpenseController::class, 'myExpenses'])->name('expenses');
        Route::get('/expenses/datatable', [ExpenseController::class, 'myExpensesAjax'])->name('expenses.datatable');
        Route::get('/expenses/create', [ExpenseController::class, 'createMyExpense'])->name('expenses.create');
        Route::post('/expenses', [ExpenseController::class, 'storeMyExpense'])->name('expenses.store');
        Route::get('/expenses/{id}', [ExpenseController::class, 'showMyExpense'])->name('expenses.show');
        Route::get('/expenses/{id}/edit', [ExpenseController::class, 'editMyExpense'])->name('expenses.edit');
        Route::put('/expenses/{id}', [ExpenseController::class, 'updateMyExpense'])->name('expenses.update');
        Route::delete('/expenses/{id}', [ExpenseController::class, 'deleteMyExpense'])->name('expenses.delete');

        // My Holidays
        Route::get('/holidays', [HolidayController::class, 'myHolidays'])->name('holidays');

        // My Compensatory Offs
        Route::get('/compensatory-offs', [CompensatoryOffController::class, 'myCompOffs'])->name('compensatory-offs');
        Route::post('/compensatory-offs', [CompensatoryOffController::class, 'requestCompOff'])->name('compensatory-offs.request');
    });

    // Legacy self-service route for backward compatibility
    Route::prefix('self-service')->name('self-service.')->group(function () {
        Route::get('/profile', function () {
            return redirect()->route('hrcore.my.profile');
        })->name('profile');
    });

    // Attendance Management (HR/Admin Functions)
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/datatable', [AttendanceController::class, 'indexAjax'])->name('datatable');
        Route::get('/web-attendance', [AttendanceController::class, 'webAttendance'])->name('web-attendance');

        // Legacy self-service redirects - redirect to new /my routes
        Route::get('/my-attendance', function () {
            return redirect()->route('hrcore.my.attendance');
        })->name('my-attendance');
        Route::get('/regularization', function () {
            return redirect()->route('hrcore.my.attendance.regularization');
        })->name('regularization');
        Route::get('/reports', function () {
            return redirect()->route('hrcore.my.attendance.reports');
        })->name('reports');
        Route::get('/today-status', [AttendanceController::class, 'getTodayStatus'])->name('today-status');
        Route::get('/global-status', [AttendanceController::class, 'getGlobalStatus'])->name('global-status');
        Route::post('/web-check-in', [AttendanceController::class, 'webCheckIn'])->name('web-check-in');
        Route::post('/web-check-out', [AttendanceController::class, 'webCheckOut'])->name('web-check-out');
        Route::get('/export', [AttendanceController::class, 'export'])->name('export');
        Route::get('/statistics', [AttendanceController::class, 'statistics'])->name('statistics');
        Route::get('/{id}/details', [AttendanceController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AttendanceController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AttendanceController::class, 'update'])->name('update');
    });

    // Attendance Regularization (HR/Admin Functions)
    Route::prefix('attendance-regularization')->name('attendance-regularization.')->group(function () {
        Route::get('/', [AttendanceRegularizationController::class, 'index'])->name('index');
        Route::get('/datatable', [AttendanceRegularizationController::class, 'indexAjax'])->name('datatable');
        Route::get('/create', [AttendanceRegularizationController::class, 'create'])->name('create');
        Route::post('/', [AttendanceRegularizationController::class, 'store'])->name('store');
        Route::get('/{id}', [AttendanceRegularizationController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AttendanceRegularizationController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AttendanceRegularizationController::class, 'update'])->name('update');
        Route::delete('/{id}', [AttendanceRegularizationController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/approve', [AttendanceRegularizationController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [AttendanceRegularizationController::class, 'reject'])->name('reject');
    });

    // Attendance Dashboard
    Route::prefix('attendance-dashboard')->name('attendance-dashboard.')->group(function () {
        Route::get('/', [AttendanceDashboardController::class, 'index'])->name('index');
        Route::get('/stats', [AttendanceDashboardController::class, 'getStats'])->name('stats');
        Route::get('/team-attendance', [AttendanceDashboardController::class, 'getTeamAttendance'])->name('team-attendance');
        Route::get('/pending-regularizations', [AttendanceDashboardController::class, 'getPendingRegularizations'])->name('pending-regularizations');
        Route::get('/attendance-summary', [AttendanceDashboardController::class, 'getAttendanceSummary'])->name('attendance-summary');
    });

    // Leave Management (HR/Admin Functions)
    Route::prefix('leaves')->name('leaves.')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('index');
        Route::get('/datatable', [LeaveController::class, 'indexAjax'])->name('datatable');
        Route::get('/create', [LeaveController::class, 'create'])->name('create');

        // Legacy self-service redirects - redirect to new /my routes
        Route::get('/apply', function () {
            return redirect()->route('hrcore.my.leaves.apply');
        })->name('apply');
        Route::get('/balance', function () {
            return redirect()->route('hrcore.my.leaves.balance');
        })->name('balance');
        Route::get('/balance/{leaveTypeId}', [LeaveController::class, 'getLeaveBalanceForType'])->name('balance.type');
        Route::get('/team', [LeaveController::class, 'teamCalendar'])->name('team');
        Route::post('/', [LeaveController::class, 'store'])->name('store');
        Route::get('/{id}', [LeaveController::class, 'showPage'])->name('show');
        Route::get('/{id}/edit', [LeaveController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LeaveController::class, 'update'])->name('update');
        Route::delete('/{id}', [LeaveController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/action', [LeaveController::class, 'actionAjax'])->name('action');
        Route::post('/{id}/approve', [LeaveController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [LeaveController::class, 'reject'])->name('reject');
        Route::post('/{id}/cancel', [LeaveController::class, 'cancel'])->name('cancel');
    });

    // Compensatory Offs
    Route::prefix('compensatory-offs')->name('compensatory-offs.')->group(function () {
        Route::get('/', [CompensatoryOffController::class, 'index'])->name('index');
        Route::get('/datatable', [CompensatoryOffController::class, 'indexAjax'])->name('datatable');
        Route::get('/create', [CompensatoryOffController::class, 'create'])->name('create');
        Route::post('/', [CompensatoryOffController::class, 'store'])->name('store');
        Route::get('/statistics', [CompensatoryOffController::class, 'statistics'])->name('statistics');
        Route::get('/{id}', [CompensatoryOffController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CompensatoryOffController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CompensatoryOffController::class, 'update'])->name('update');
        Route::delete('/{id}', [CompensatoryOffController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/approve', [CompensatoryOffController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [CompensatoryOffController::class, 'reject'])->name('reject');
    });

    // Leave Types
    Route::prefix('leave-types')->name('leave-types.')->group(function () {
        Route::get('/', [LeaveTypeController::class, 'index'])->name('index');
        Route::get('/datatable', [LeaveTypeController::class, 'indexAjax'])->name('datatable');
        Route::get('/create', [LeaveTypeController::class, 'create'])->name('create');
        Route::post('/', [LeaveTypeController::class, 'store'])->name('store');
        Route::get('/{id}', [LeaveTypeController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [LeaveTypeController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LeaveTypeController::class, 'update'])->name('update');
        Route::delete('/{id}', [LeaveTypeController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [LeaveTypeController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/check-code', [LeaveTypeController::class, 'checkCodeValidationAjax'])->name('check-code');
    });

    // Leave Balance Management
    Route::prefix('leave-balance')->name('leave-balance.')->group(function () {
        Route::get('/', [Modules\HRCore\app\Http\Controllers\LeaveBalanceController::class, 'index'])->name('index');
        Route::get('/datatable', [Modules\HRCore\app\Http\Controllers\LeaveBalanceController::class, 'indexAjax'])->name('datatable');
        Route::get('/summary', [Modules\HRCore\app\Http\Controllers\LeaveBalanceController::class, 'getBalanceSummary'])->name('summary');
        Route::get('/{employeeId}', [Modules\HRCore\app\Http\Controllers\LeaveBalanceController::class, 'show'])->name('show');
        Route::post('/set-initial', [Modules\HRCore\app\Http\Controllers\LeaveBalanceController::class, 'setInitialBalance'])->name('set-initial');
        Route::post('/adjust', [Modules\HRCore\app\Http\Controllers\LeaveBalanceController::class, 'adjustBalance'])->name('adjust');
        Route::post('/bulk-set', [Modules\HRCore\app\Http\Controllers\LeaveBalanceController::class, 'bulkSetInitialBalance'])->name('bulk-set');
    });

    // Shifts
    Route::prefix('shifts')->name('shifts.')->group(function () {
        Route::get('/', [ShiftController::class, 'index'])->name('index');
        Route::get('/datatable', [ShiftController::class, 'listAjax'])->name('datatable');
        Route::get('/create', [ShiftController::class, 'create'])->name('create');
        Route::post('/', [ShiftController::class, 'store'])->name('store');
        Route::get('/{id}', [ShiftController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ShiftController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ShiftController::class, 'update'])->name('update');
        Route::delete('/{id}', [ShiftController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [ShiftController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/active-list', [ShiftController::class, 'getActiveShiftsForDropdown'])->name('active-list');
    });

    // Departments
    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [DepartmentsController::class, 'index'])->name('index');
        Route::get('/datatable', [DepartmentsController::class, 'indexAjax'])->name('datatable');
        Route::get('/create', [DepartmentsController::class, 'create'])->name('create');
        Route::get('/parent-list', [DepartmentsController::class, 'getParentDepartments'])->name('parent-list');
        Route::get('/list', [DepartmentsController::class, 'getListAjax'])->name('list');
        Route::post('/', [DepartmentsController::class, 'store'])->name('store');
        Route::get('/{id}', [DepartmentsController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [DepartmentsController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DepartmentsController::class, 'update'])->name('update');
        Route::delete('/{id}', [DepartmentsController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [DepartmentsController::class, 'toggleStatus'])->name('toggle-status');

        // Legacy routes for backward compatibility
        Route::post('/addOrUpdateDepartmentAjax', [DepartmentsController::class, 'addOrUpdateDepartmentAjax'])->name('addOrUpdateDepartmentAjax');
        Route::get('/getDepartmentAjax/{id}', [DepartmentsController::class, 'getDepartmentAjax'])->name('getDepartmentAjax');
        Route::delete('/deleteAjax/{id}', [DepartmentsController::class, 'deleteAjax'])->name('deleteAjax');
        Route::post('/changeStatus/{id}', [DepartmentsController::class, 'changeStatus'])->name('changeStatus');
        Route::get('/getParentDepartments', [DepartmentsController::class, 'getParentDepartments'])->name('getParentDepartments');
        Route::get('/indexAjax', [DepartmentsController::class, 'indexAjax'])->name('indexAjax');
    });

    // Designations
    Route::prefix('designations')->name('designations.')->group(function () {
        Route::get('/', [DesignationController::class, 'index'])->name('index');
        Route::get('/datatable', [DesignationController::class, 'indexAjax'])->name('datatable');
        Route::get('/create', [DesignationController::class, 'create'])->name('create');
        Route::get('/list', [DesignationController::class, 'getDesignationListAjax'])->name('list');
        Route::get('/check-code', [DesignationController::class, 'checkCodeValidationAjax'])->name('check-code');
        Route::post('/', [DesignationController::class, 'store'])->name('store');
        Route::get('/{id}', [DesignationController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [DesignationController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DesignationController::class, 'update'])->name('update');
        Route::delete('/{id}', [DesignationController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [DesignationController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Teams
    Route::prefix('teams')->name('teams.')->group(function () {
        Route::get('/', [TeamController::class, 'index'])->name('index');
        Route::get('/datatable', [TeamController::class, 'getTeamsListAjax'])->name('datatable');
        Route::get('/create', [TeamController::class, 'create'])->name('create');
        Route::get('/list', [TeamController::class, 'getTeamListAjax'])->name('list');
        Route::get('/check-code', [TeamController::class, 'checkCodeValidationAjax'])->name('check-code');
        Route::post('/', [TeamController::class, 'store'])->name('store');
        Route::get('/{id}', [TeamController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [TeamController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TeamController::class, 'update'])->name('update');
        Route::delete('/{id}', [TeamController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [TeamController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Expense Types
    Route::prefix('expense-types')->name('expense-types.')->group(function () {
        Route::get('/', [ExpenseTypeController::class, 'index'])->name('index');
        Route::get('/datatable', [ExpenseTypeController::class, 'indexAjax'])->name('datatable');
        Route::post('/', [ExpenseTypeController::class, 'store'])->name('store');
        Route::get('/{id}', [ExpenseTypeController::class, 'show'])->name('show');
        Route::put('/{id}', [ExpenseTypeController::class, 'update'])->name('update');
        Route::delete('/{id}', [ExpenseTypeController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [ExpenseTypeController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/check-code', [ExpenseTypeController::class, 'checkCodeValidationAjax'])->name('check-code');
    });

    // Expense Management (HR/Admin Functions)
    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('/datatable', [ExpenseController::class, 'indexAjax'])->name('datatable');
        Route::get('/create', [ExpenseController::class, 'create'])->name('create');
        Route::post('/', [ExpenseController::class, 'store'])->name('store');

        // Legacy self-service redirects - redirect to new /my routes
        Route::get('/my-expenses', function () {
            return redirect()->route('hrcore.my.expenses');
        })->name('my-expenses');
        Route::get('/my-expenses/datatable', function () {
            return redirect()->route('hrcore.my.expenses.datatable');
        })->name('my-expenses.datatable');
        Route::get('/{id}', [ExpenseController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ExpenseController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ExpenseController::class, 'update'])->name('update');
        Route::delete('/{id}', [ExpenseController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/approve', [ExpenseController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [ExpenseController::class, 'reject'])->name('reject');
        Route::post('/{id}/process', [ExpenseController::class, 'process'])->name('process');
    });

    // Holidays (HR/Admin Functions)
    Route::prefix('holidays')->name('holidays.')->group(function () {
        Route::get('/', [HolidayController::class, 'index'])->name('index');

        // Legacy self-service redirect - redirect to new /my routes
        Route::get('/my-holidays', function () {
            return redirect()->route('hrcore.my.holidays');
        })->name('my-holidays');
        Route::get('/datatable', [HolidayController::class, 'indexAjax'])->name('datatable');
        Route::get('/create', [HolidayController::class, 'create'])->name('create');
        Route::post('/', [HolidayController::class, 'store'])->name('store');
        Route::get('/{id}', [HolidayController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [HolidayController::class, 'edit'])->name('edit');
        Route::put('/{id}', [HolidayController::class, 'update'])->name('update');
        Route::delete('/{id}', [HolidayController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [HolidayController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Organization Hierarchy
    Route::prefix('organization-hierarchy')->name('organization-hierarchy.')->group(function () {
        Route::get('/', [OrganisationHierarchyController::class, 'index'])->name('index');
        Route::get('/data', [OrganisationHierarchyController::class, 'getData'])->name('data');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::post('/attendance', [ReportController::class, 'getAttendanceReport'])->name('attendance');
        Route::post('/leaves', [ReportController::class, 'getLeaveReport'])->name('leaves');
        Route::post('/expenses', [ReportController::class, 'getExpenseReport'])->name('expenses');
        Route::post('/visits', [ReportController::class, 'getVisitReport'])->name('visits');
        Route::post('/product-orders', [ReportController::class, 'getProductOrderReport'])->name('product-orders');
    });

    // API/AJAX endpoints that need consistent naming
    Route::prefix('ajax')->name('ajax.')->group(function () {
        // Common AJAX operations
        Route::post('/store-update', function () {
            // This is a placeholder for handling all AJAX store/update operations
            // Each controller will implement its own logic
        })->name('store-update');
    });
});

// Legacy route redirects for backward compatibility
Route::group(['middleware' => ['auth', 'web']], function () {
    // Redirect old routes to new standardized routes
    Route::get('employees', function () {
        return redirect()->route('hrcore.employees.index');
    });
    Route::get('attendance', function () {
        return redirect()->route('hrcore.attendance.index');
    });
    Route::get('leaves', function () {
        return redirect()->route('hrcore.leaves.index');
    });
    Route::get('shifts', function () {
        return redirect()->route('hrcore.shifts.index');
    });
    Route::get('expenses', function () {
        return redirect()->route('hrcore.expenses.index');
    });
    Route::get('departments', function () {
        return redirect()->route('hrcore.departments.index');
    });
    Route::get('designations', function () {
        return redirect()->route('hrcore.designations.index');
    });
    Route::get('teams', function () {
        return redirect()->route('hrcore.teams.index');
    });
    Route::get('holidays', function () {
        return redirect()->route('hrcore.holidays.index');
    });
});
