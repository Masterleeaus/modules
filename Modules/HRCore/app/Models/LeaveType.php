<?php

namespace Modules\HRCore\app\Models;

use App\Enums\Status;
use App\Traits\UserActionsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class LeaveType extends Model implements AuditableContract
{
    use Auditable, SoftDeletes, UserActionsTrait;

    protected $table = 'leave_types';

    protected $fillable = [
        'name',
        'code',
        'notes',
        'is_proof_required',
        'status',
        'is_accrual_enabled',
        'accrual_frequency',
        'accrual_rate',
        'max_accrual_limit',
        'allow_carry_forward',
        'max_carry_forward',
        'carry_forward_expiry_months',
        'allow_encashment',
        'max_encashment_days',
        'is_comp_off_type',
        'tenant_id',
        'created_by_id',
        'updated_by_id',
    ];

    protected $casts = [
        'status' => Status::class,
        'is_proof_required' => 'boolean',
        'is_accrual_enabled' => 'boolean',
        'allow_carry_forward' => 'boolean',
        'allow_encashment' => 'boolean',
        'is_comp_off_type' => 'boolean',
        'accrual_rate' => 'decimal:2',
        'max_accrual_limit' => 'decimal:2',
        'max_carry_forward' => 'decimal:2',
        'max_encashment_days' => 'decimal:2',
    ];

    /**
     * Get all leave requests for this leave type
     */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'leave_type_id');
    }

    /**
     * Get all leave accruals for this leave type
     */
    public function leaveAccruals()
    {
        return $this->hasMany(LeaveAccrual::class, 'leave_type_id');
    }

    /**
     * Check if this is a compensatory off type
     */
    public function isCompOffType()
    {
        return $this->is_comp_off_type;
    }

    /**
     * Get accrual rate per month
     */
    public function getMonthlyAccrualRate()
    {
        switch ($this->accrual_frequency) {
            case 'monthly':
                return $this->accrual_rate;
            case 'quarterly':
                return $this->accrual_rate / 3;
            case 'yearly':
                return $this->accrual_rate / 12;
            default:
                return 0;
        }
    }
}
