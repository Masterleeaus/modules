<?php

namespace Modules\HRCore\app\Models;

use App\Models\User;
use App\Traits\UserActionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class AttendanceRegularization extends Model implements AuditableContract
{
    use Auditable, HasFactory, SoftDeletes, UserActionsTrait;

    /**
     * The table associated with the model.
     */
    protected $table = 'attendance_regularizations';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'attendance_id',
        'date',
        'type',
        'requested_check_in_time',
        'requested_check_out_time',
        'actual_check_in_time',
        'actual_check_out_time',
        'reason',
        'manager_comments',
        'status',
        'approved_by',
        'approved_at',
        'attachments',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date' => 'date',
        'requested_check_in_time' => 'datetime:H:i',
        'requested_check_out_time' => 'datetime:H:i',
        'actual_check_in_time' => 'datetime:H:i',
        'actual_check_out_time' => 'datetime:H:i',
        'approved_at' => 'datetime',
        'attachments' => 'array',
    ];

    /**
     * Get the user that owns the regularization request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attendance record associated with this regularization.
     */
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    /**
     * Get the manager who approved the regularization.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the regularization type label.
     */
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'missing_checkin' => __('Missing Check-in'),
            'missing_checkout' => __('Missing Check-out'),
            'wrong_time' => __('Wrong Time'),
            'forgot_punch' => __('Forgot to Punch'),
            'other' => __('Other'),
            default => $this->type,
        };
    }

    /**
     * Get the status label.
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending' => __('Pending'),
            'approved' => __('Approved'),
            'rejected' => __('Rejected'),
            default => $this->status,
        };
    }

    /**
     * Get the status badge class.
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'pending' => 'bg-label-warning',
            'approved' => 'bg-label-success',
            'rejected' => 'bg-label-danger',
            default => 'bg-label-secondary',
        };
    }

    /**
     * Scope to filter by status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}
