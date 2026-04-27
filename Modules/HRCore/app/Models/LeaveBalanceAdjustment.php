<?php

namespace Modules\HRCore\app\Models;

use App\Models\User;
use App\Traits\UserActionsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class LeaveBalanceAdjustment extends Model implements AuditableContract
{
    use Auditable, UserActionsTrait;

    protected $table = 'leave_balance_adjustments';

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'adjustment_type',
        'days',
        'reason',
        'year',
        'effective_date',
        'balance_before',
        'balance_after',
        'created_by_id',
        'updated_by_id',
        'tenant_id',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'days' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'year' => 'integer',
    ];

    /**
     * Get the user for this adjustment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the leave type for this adjustment
     */
    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }
}
