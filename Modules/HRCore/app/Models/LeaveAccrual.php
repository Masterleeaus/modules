<?php

namespace Modules\HRCore\app\Models;

use App\Models\User;
use App\Traits\UserActionsTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveAccrual extends Model
{
    use UserActionsTrait;

    protected $table = 'leave_accruals';

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'accrual_date',
        'accrued_days',
        'balance_before',
        'balance_after',
        'notes',
        'created_by_id',
        'updated_by_id',
    ];

    protected $casts = [
        'accrual_date' => 'date',
        'accrued_days' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    /**
     * Get the user for this accrual
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the leave type for this accrual
     */
    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Process accruals for all employees
     */
    public static function processAccruals($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();
        $leaveTypes = LeaveType::where('is_accrual_enabled', true)
            ->where('status', 'active')
            ->get();

        $processedCount = 0;

        foreach ($leaveTypes as $leaveType) {
            // Check if we should accrue based on frequency
            if (! self::shouldAccrue($leaveType, $date)) {
                continue;
            }

            // Get all active employees
            $employees = User::whereDoesntHave('roles', function ($q) {
                $q->where('name', 'client');
            })->where('status', 'active')->get();

            foreach ($employees as $employee) {
                self::accrueForEmployee($employee, $leaveType, $date);
                $processedCount++;
            }
        }

        return $processedCount;
    }

    /**
     * Check if accrual should happen based on frequency
     */
    private static function shouldAccrue(LeaveType $leaveType, Carbon $date): bool
    {
        switch ($leaveType->accrual_frequency) {
            case 'monthly':
                // Accrue on the 1st of each month
                return $date->day === 1;

            case 'quarterly':
                // Accrue on the 1st day of each quarter
                return $date->day === 1 && in_array($date->month, [1, 4, 7, 10]);

            case 'yearly':
                // Accrue on January 1st
                return $date->day === 1 && $date->month === 1;

            default:
                return false;
        }
    }

    /**
     * Process accrual for a specific employee and leave type
     */
    private static function accrueForEmployee(User $employee, LeaveType $leaveType, Carbon $date)
    {
        $currentYear = $date->year;
        $currentBalance = $employee->getLeaveBalance($leaveType->id);

        // Check if already accrued for this period
        $existingAccrual = self::where('user_id', $employee->id)
            ->where('leave_type_id', $leaveType->id)
            ->where('accrual_date', $date->format('Y-m-d'))
            ->first();

        if ($existingAccrual) {
            return;
        }

        // Calculate accrual amount
        $accrualAmount = $leaveType->accrual_rate;

        // Check max accrual limit
        if ($leaveType->max_accrual_limit && ($currentBalance + $accrualAmount) > $leaveType->max_accrual_limit) {
            $accrualAmount = max(0, $leaveType->max_accrual_limit - $currentBalance);
        }

        if ($accrualAmount <= 0) {
            return;
        }

        // Create accrual record
        self::create([
            'user_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'accrual_date' => $date,
            'accrued_days' => $accrualAmount,
            'balance_before' => $currentBalance,
            'balance_after' => $currentBalance + $accrualAmount,
            'notes' => 'Automatic accrual - '.$leaveType->accrual_frequency,
        ]);

        // Update user available leave
        $availableLeave = UserAvailableLeave::firstOrCreate(
            [
                'user_id' => $employee->id,
                'leave_type_id' => $leaveType->id,
                'year' => $currentYear,
            ],
            [
                'entitled_leaves' => 0,
                'available_leaves' => 0,
            ]
        );

        $availableLeave->available_leaves += $accrualAmount;
        $availableLeave->save();
    }

    /**
     * Get current balance for a user and leave type
     */
    public static function getCurrentBalance($userId, $leaveTypeId)
    {
        $currentYear = Carbon::now()->year;

        $availableLeave = UserAvailableLeave::where('user_id', $userId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $currentYear)
            ->first();

        return $availableLeave ? $availableLeave->available_leaves : 0;
    }
}
