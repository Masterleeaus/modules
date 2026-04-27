<?php

namespace Modules\HRCore\app\Http\Controllers;

use App\ApiClasses\Error;
use App\ApiClasses\Success;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\HRCore\app\Models\LeaveBalanceAdjustment;
use Modules\HRCore\app\Models\LeaveType;
use Modules\HRCore\app\Models\UserAvailableLeave;

class LeaveBalanceController extends Controller
{
    /**
     * Create a new controller instance
     */
    public function __construct()
    {
        $this->middleware('permission:hrcore.view-leave-balances')->only(['index', 'indexAjax', 'show']);
        $this->middleware('permission:hrcore.manage-leave-balances')->only(['setInitialBalance', 'adjustBalance', 'bulkSetInitialBalance']);
        $this->middleware('permission:hrcore.view-leave-reports')->only(['getBalanceSummary']);
    }

    /**
     * Display leave balance management page
     */
    public function index()
    {

        return view('hrcore::leave-balance.index');
    }

    /**
     * Get employee leave balances for DataTable
     */
    public function indexAjax(Request $request)
    {

        $query = User::with(['designation', 'team'])
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'client');
            });

        // Apply filters
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('id', $request->employee_id);
        }

        if ($request->has('team_id') && $request->team_id) {
            $query->where('team_id', $request->team_id);
        }

        $employees = $query->get();
        $leaveTypes = LeaveType::where('status', 'active')->get();

        $data = [];
        foreach ($employees as $employee) {
            $row = [
                'id' => $employee->id,
                'employee' => $employee->getFullName(),
                'code' => $employee->code,
                'designation' => $employee->designation->name ?? '-',
                'team' => $employee->team->name ?? '-',
                'balances' => [],
            ];

            foreach ($leaveTypes as $leaveType) {
                $balance = $employee->getLeaveBalance($leaveType->id);
                $row['balances'][$leaveType->code] = $balance;
            }

            $data[] = $row;
        }

        return response()->json([
            'data' => $data,
        ]);
    }

    /**
     * Show employee leave balance details
     */
    public function show($employeeId)
    {
        Gate::authorize('hrcore.view-leave-balances');

        $employee = User::findOrFail($employeeId);
        $leaveTypes = LeaveType::where('status', 'active')->get();
        $currentYear = Carbon::now()->year;

        $balances = [];

        foreach ($leaveTypes as $leaveType) {
            $availableLeave = UserAvailableLeave::where('user_id', $employee->id)
                ->where('leave_type_id', $leaveType->id)
                ->where('year', $currentYear)
                ->first();

            $balances[] = [
                'leaveType' => $leaveType,
                'availableLeave' => $availableLeave,
                'currentBalance' => $employee->getLeaveBalance($leaveType->id),
            ];
        }

        $adjustments = LeaveBalanceAdjustment::where('user_id', $employee->id)
            ->with(['leaveType', 'createdBy'])
            ->latest()
            ->limit(20)
            ->get();

        return view('hrcore::leave-balance.show', compact('employee', 'balances', 'adjustments', 'currentYear'));
    }

    /**
     * Set initial leave balance for an employee
     */
    public function setInitialBalance(Request $request)
    {
        Gate::authorize('hrcore.manage-leave-balances');

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'year' => 'required|integer|min:2020|max:2100',
            'entitled_leaves' => 'required|numeric|min:0|max:365',
        ]);

        if ($validator->fails()) {
            return Error::response($validator->errors()->first());
        }

        try {
            DB::beginTransaction();

            $availableLeave = UserAvailableLeave::updateOrCreate(
                [
                    'user_id' => $request->user_id,
                    'leave_type_id' => $request->leave_type_id,
                    'year' => $request->year,
                ],
                [
                    'entitled_leaves' => $request->entitled_leaves,
                    'available_leaves' => $request->entitled_leaves,
                    'created_by_id' => auth()->id(),
                    'updated_by_id' => auth()->id(),
                ]
            );

            // Create adjustment record
            LeaveBalanceAdjustment::create([
                'user_id' => $request->user_id,
                'leave_type_id' => $request->leave_type_id,
                'adjustment_type' => 'add',
                'days' => $request->entitled_leaves,
                'reason' => 'Initial balance setup',
                'year' => $request->year,
                'effective_date' => now(),
                'balance_before' => 0,
                'balance_after' => $request->entitled_leaves,
                'created_by_id' => auth()->id(),
                'updated_by_id' => auth()->id(),
            ]);

            DB::commit();

            return Success::response('Initial balance set successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to set initial balance: '.$e->getMessage());

            return Error::response('Failed to set initial balance');
        }
    }

    /**
     * Adjust leave balance
     */
    public function adjustBalance(Request $request)
    {
        Gate::authorize('hrcore.manage-leave-balances');

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'adjustment_type' => 'required|in:add,deduct',
            'days' => 'required|numeric|min:0.5|max:365',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return Error::response($validator->errors()->first());
        }

        try {
            DB::beginTransaction();

            $currentYear = Carbon::now()->year;
            $employee = User::findOrFail($request->user_id);
            $currentBalance = $employee->getLeaveBalance($request->leave_type_id);

            // Calculate new balance
            $adjustment = $request->adjustment_type === 'add' ? $request->days : -$request->days;
            $newBalance = $currentBalance + $adjustment;

            if ($newBalance < 0) {
                return Error::response('Adjustment would result in negative balance');
            }

            // Update available leave record
            $availableLeave = UserAvailableLeave::where('user_id', $request->user_id)
                ->where('leave_type_id', $request->leave_type_id)
                ->where('year', $currentYear)
                ->first();

            if (! $availableLeave) {
                $availableLeave = UserAvailableLeave::create([
                    'user_id' => $request->user_id,
                    'leave_type_id' => $request->leave_type_id,
                    'year' => $currentYear,
                    'entitled_leaves' => 0,
                    'additional_leaves' => 0,
                    'available_leaves' => 0,
                    'created_by_id' => auth()->id(),
                ]);
            }

            $availableLeave->additional_leaves += $adjustment;
            $availableLeave->available_leaves = $newBalance;

            $availableLeave->updated_by_id = auth()->id();
            $availableLeave->save();

            // Create adjustment record
            LeaveBalanceAdjustment::create([
                'user_id' => $request->user_id,
                'leave_type_id' => $request->leave_type_id,
                'adjustment_type' => $request->adjustment_type,
                'days' => $request->days,
                'reason' => $request->reason,
                'year' => $currentYear,
                'effective_date' => now(),
                'balance_before' => $currentBalance,
                'balance_after' => $newBalance,
                'created_by_id' => auth()->id(),
                'updated_by_id' => auth()->id(),
            ]);

            DB::commit();

            return Success::response('Balance adjusted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to adjust balance: '.$e->getMessage());

            return Error::response('Failed to adjust balance');
        }
    }

    /**
     * Bulk set initial balances
     */
    public function bulkSetInitialBalance(Request $request)
    {
        Gate::authorize('hrcore.manage-leave-balances');

        $validator = Validator::make($request->all(), [
            'year' => 'required|integer|min:2020|max:2100',
            'leave_type_id' => 'required|exists:leave_types,id',
            'employees' => 'required|array',
            'employees.*.user_id' => 'required|exists:users,id',
            'employees.*.entitled_leaves' => 'required|numeric|min:0|max:365',
        ]);

        if ($validator->fails()) {
            return Error::response($validator->errors()->first());
        }

        try {
            DB::beginTransaction();

            $successCount = 0;
            foreach ($request->employees as $employeeData) {
                $availableLeave = UserAvailableLeave::updateOrCreate(
                    [
                        'user_id' => $employeeData['user_id'],
                        'leave_type_id' => $request->leave_type_id,
                        'year' => $request->year,
                    ],
                    [
                        'entitled_leaves' => $employeeData['entitled_leaves'],
                        'available_leaves' => $employeeData['entitled_leaves'],
                        'created_by_id' => auth()->id(),
                        'updated_by_id' => auth()->id(),
                    ]
                );

                // Create adjustment record
                LeaveBalanceAdjustment::create([
                    'user_id' => $employeeData['user_id'],
                    'leave_type_id' => $request->leave_type_id,
                    'adjustment_type' => 'add',
                    'days' => $employeeData['entitled_leaves'],
                    'reason' => 'Bulk initial balance setup',
                    'year' => $request->year,
                    'effective_date' => now(),
                    'balance_before' => 0,
                    'balance_after' => $employeeData['entitled_leaves'],
                    'created_by_id' => auth()->id(),
                    'updated_by_id' => auth()->id(),
                ]);

                $successCount++;
            }

            DB::commit();

            return Success::response("Initial balance set for {$successCount} employees");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to set bulk initial balance: '.$e->getMessage());

            return Error::response('Failed to set bulk initial balance');
        }
    }

    /**
     * Get balance summary for reports
     */
    public function getBalanceSummary(Request $request)
    {
        Gate::authorize('hrcore.view-leave-reports');

        $query = User::with(['designation', 'team'])
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'client');
            });

        // Apply filters
        if ($request->has('team_id') && $request->team_id) {
            $query->where('team_id', $request->team_id);
        }

        if ($request->has('department_id') && $request->department_id) {
            $query->whereHas('designation', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $employees = $query->get();
        $leaveTypes = LeaveType::where('status', 'active')->get();

        $summary = [];
        foreach ($leaveTypes as $leaveType) {
            $summary[$leaveType->name] = [
                'total_entitled' => 0,
                'total_used' => 0,
                'total_available' => 0,
                'total_pending' => 0,
            ];
        }

        foreach ($employees as $employee) {
            foreach ($leaveTypes as $leaveType) {
                $availableLeave = UserAvailableLeave::where('user_id', $employee->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->where('year', Carbon::now()->year)
                    ->first();

                if ($availableLeave) {
                    $summary[$leaveType->name]['total_entitled'] += $availableLeave->entitled_leaves;
                    $summary[$leaveType->name]['total_used'] += $availableLeave->used_leaves;
                    $summary[$leaveType->name]['total_available'] += $availableLeave->available_leaves;
                }

                // Get pending leaves
                $pendingLeaves = \Modules\HRCore\app\Models\LeaveRequest::where('user_id', $employee->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->where('status', 'pending')
                    ->sum('total_days');

                $summary[$leaveType->name]['total_pending'] += $pendingLeaves;
            }
        }

        return view('hrcore::leave-balance.summary', compact('summary', 'employees'));
    }
}
