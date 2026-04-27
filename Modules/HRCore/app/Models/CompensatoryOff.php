<?php

namespace Modules\HRCore\app\Models;

use App\Models\User;
use App\Traits\UserActionsTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class CompensatoryOff extends Model implements AuditableContract
{
    use Auditable, HasFactory, UserActionsTrait;

    protected $table = 'compensatory_offs';

    protected $fillable = [
        'user_id',
        'worked_date',
        'hours_worked',
        'comp_off_days',
        'reason',
        'expiry_date',
        'is_used',
        'used_date',
        'leave_request_id',
        'status',
        'approved_by_id',
        'approved_at',
        'approval_notes',
        'created_by_id',
        'updated_by_id',
        'tenant_id',
    ];

    protected $casts = [
        'worked_date' => 'date',
        'expiry_date' => 'date',
        'used_date' => 'date',
        'approved_at' => 'datetime',
        'is_used' => 'boolean',
        'hours_worked' => 'decimal:2',
        'comp_off_days' => 'decimal:2',
    ];

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Set expiry date if not provided
        static::creating(function ($compOff) {
            if (! $compOff->expiry_date) {
                // Default expiry is 3 months from worked date
                $compOff->expiry_date = Carbon::parse($compOff->worked_date)->addMonths(3);
            }
        });
    }

    /**
     * Get the user that owns the compensatory off
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the approver
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    /**
     * Get the leave request where this comp off was used
     */
    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    /**
     * Scope for available comp offs
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'approved')
            ->where('is_used', false)
            ->where('expiry_date', '>=', now()->toDateString());
    }

    /**
     * Scope for expired comp offs
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'approved')
            ->where('is_used', false)
            ->where('expiry_date', '<', now()->toDateString());
    }

    /**
     * Mark as used
     */
    public function markAsUsed($leaveRequestId = null)
    {
        $this->is_used = true;
        $this->used_date = now();
        $this->leave_request_id = $leaveRequestId;
        $this->save();
    }

    /**
     * Check if can be used
     */
    public function canBeUsed()
    {
        return $this->status === 'approved'
          && ! $this->is_used
          && $this->expiry_date >= now()->toDateString();
    }

    /**
     * Submit for approval
     */
    public function submitForApproval()
    {
        $this->status = 'pending';
        $this->save();

        // Create an approval request
        $this->requestApproval('comp_off', auth()->user());

        return $this;
    }

    /**
     * Process approval result
     */
    public function processApprovalResult()
    {
        if ($this->isApproved()) {
            $this->status = 'approved';
            $this->approved_by_id = auth()->id();
            $this->approved_at = now();
            $this->save();

            return true;
        } elseif ($this->isRejected()) {
            $this->status = 'rejected';
            $this->save();

            return true;
        }

        return false;
    }

    /**
     * Get available comp off balance for a user
     */
    public static function getAvailableBalance($userId)
    {
        return self::where('user_id', $userId)
            ->available()
            ->sum('comp_off_days');
    }
}
