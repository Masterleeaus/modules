<?php

namespace Modules\HRCore\app\Models;

use App\Enums\LeaveRequestStatus;
use App\Models\User;
use App\Traits\UserActionsTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\FileManagerCore\Traits\HasFiles;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class LeaveRequest extends Model implements AuditableContract
{
    use Auditable, HasFiles, SoftDeletes, UserActionsTrait;

    protected $table = 'leave_requests';

    protected $fillable = [
        'from_date',
        'to_date',
        'user_id',
        'leave_type_id',
        'is_half_day',
        'half_day_type',
        'total_days',
        'document',
        'user_notes',
        'emergency_contact',
        'emergency_phone',
        'is_abroad',
        'abroad_location',
        'approved_by_id',
        'rejected_by_id',
        'approved_at',
        'rejected_at',
        'status',
        'approval_notes',
        'notes',
        'created_by_id',
        'updated_by_id',
        'tenant_id',
        'cancel_reason',
        'cancelled_at',
        'cancelled_by_id',
    ];

    protected $casts = [
        'status' => LeaveRequestStatus::class,
        'from_date' => 'date:d-m-Y',
        'to_date' => 'date:d-m-Y',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'is_half_day' => 'boolean',
        'is_abroad' => 'boolean',
        'total_days' => 'decimal:2',
    ];

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Calculate total days before saving
        static::saving(function ($leaveRequest) {
            $leaveRequest->calculateTotalDays();
        });
    }

    /**
     * Calculate total days based on dates and half-day settings
     */
    public function calculateTotalDays()
    {
        if ($this->is_half_day) {
            $this->total_days = 0.5;
            // For half day, from_date and to_date should be same
            $this->to_date = $this->from_date;
        } else {
            $fromDate = Carbon::parse($this->from_date);
            $toDate = Carbon::parse($this->to_date);

            // Calculate working days (excluding weekends)
            $totalDays = 0;
            $currentDate = $fromDate->copy();

            while ($currentDate->lte($toDate)) {
                // Check if it's not a weekend (customize based on your business rules)
                if (! $currentDate->isWeekend()) {
                    $totalDays++;
                }
                $currentDate->addDay();
            }

            $this->total_days = $totalDays;
        }
    }

    /**
     * Get the half day display text
     */
    public function getHalfDayDisplayAttribute()
    {
        if (! $this->is_half_day) {
            return null;
        }

        return $this->half_day_type === 'first_half' ? __('First Half') : __('Second Half');
    }

    /**
     * Get the date range display
     */
    public function getDateRangeDisplayAttribute()
    {
        if ($this->is_half_day) {
            return $this->from_date->format('d M Y').' ('.$this->half_day_display.')';
        }

        if ($this->from_date->eq($this->to_date)) {
            return $this->from_date->format('d M Y');
        }

        return $this->from_date->format('d M Y').' - '.$this->to_date->format('d M Y');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by_id');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by_id');
    }

    /**
     * Submit the leave request for approval.
     *
     * @return $this
     */
    public function submitForApproval()
    {
        $this->status = LeaveRequestStatus::PENDING;
        $this->save();

        // Create an approval request
        $this->requestApproval('leave', auth()->user());

        return $this;
    }

    /**
     * Process the approval based on the multilevel approval status.
     *
     * @return bool
     */
    public function processApprovalResult()
    {
        if ($this->isApproved()) {
            $this->status = LeaveRequestStatus::APPROVED;
            $this->approved_by_id = auth()->id();
            $this->approved_at = now();
            $this->save();

            return true;
        } elseif ($this->isRejected()) {
            $this->status = LeaveRequestStatus::REJECTED;
            $this->rejected_by_id = auth()->id();
            $this->rejected_at = now();
            $this->save();

            return true;
        }

        return false;
    }

    /**
     * Check if the leave request can be processed.
     *
     * @return bool
     */
    public function canBeProcessed()
    {
        return $this->isApproved() || $this->isRejected();
    }

    /**
     * Cancel the leave request
     */
    public function cancel($reason, $byAdmin = false)
    {
        $this->status = $byAdmin ? LeaveRequestStatus::CANCELLED_BY_ADMIN : LeaveRequestStatus::CANCELLED;
        $this->cancel_reason = $reason;
        $this->cancelled_at = now();
        $this->cancelled_by_id = auth()->id();
        $this->save();
    }

    /**
     * Check if leave can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->status, [LeaveRequestStatus::PENDING, LeaveRequestStatus::APPROVED])
          && $this->from_date->isFuture();
    }

    /**
     * Check if leave overlaps with existing leaves
     */
    public function hasOverlappingLeave()
    {
        return self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->whereIn('status', [LeaveRequestStatus::PENDING, LeaveRequestStatus::APPROVED])
            ->where(function ($query) {
                $query->whereBetween('from_date', [$this->from_date, $this->to_date])
                    ->orWhereBetween('to_date', [$this->from_date, $this->to_date])
                    ->orWhere(function ($q) {
                        $q->where('from_date', '<=', $this->from_date)
                            ->where('to_date', '>=', $this->to_date);
                    });
            })
            ->exists();
    }

    /**
     * Get the leave document file from FileManagerCore
     */
    public function getLeaveDocumentFile()
    {
        if (trait_exists('Modules\FileManagerCore\Traits\HasFiles')) {
            return $this->fileByType(\Modules\FileManagerCore\Enums\FileType::LEAVE_DOCUMENT);
        }

        return null;
    }

    /**
     * Get the leave document URL
     */
    public function getLeaveDocumentUrl()
    {
        // Try FileManagerCore first
        $file = $this->getLeaveDocumentFile();
        if ($file && method_exists($file, 'getUrl')) {
            return $file->getUrl();
        }

        // Fallback to legacy document field
        if ($this->document) {
            return \Storage::disk('public')->url($this->document);
        }

        return null;
    }
}
