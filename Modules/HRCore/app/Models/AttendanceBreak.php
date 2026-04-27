<?php

namespace Modules\HRCore\app\Models;

use App\Models\User;
use App\Traits\UserActionsTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class AttendanceBreak extends Model implements AuditableContract
{
    use Auditable, HasFactory, SoftDeletes, UserActionsTrait;

    protected $table = 'attendance_breaks';

    protected $fillable = [
        'attendance_id',
        'user_id',
        'date',
        'start_time',
        'end_time',
        'duration',
        'scheduled_duration',
        'exceeded_limit',
        'break_type',
        'reason',
        'notes',
        'status',
        'is_paid',
        'is_scheduled',
        // Location tracking
        'start_latitude',
        'start_longitude',
        'start_location',
        'end_latitude',
        'end_longitude',
        'end_location',
        // Device tracking
        'start_device_type',
        'end_device_type',
        'start_ip',
        'end_ip',
        // Validation
        'is_valid',
        'validation_error',
        'requires_approval',
        'approved_by_id',
        'approved_at',
        'approval_notes',
        // Alert tracking
        'alert_sent',
        'alert_sent_at',
        // Metadata
        'metadata',
        // Audit
        'created_by_id',
        'updated_by_id',
        'tenant_id',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration' => 'decimal:2',
        'scheduled_duration' => 'decimal:2',
        'exceeded_limit' => 'boolean',
        'is_paid' => 'boolean',
        'is_scheduled' => 'boolean',
        'start_latitude' => 'float',
        'start_longitude' => 'float',
        'end_latitude' => 'float',
        'end_longitude' => 'float',
        'is_valid' => 'boolean',
        'requires_approval' => 'boolean',
        'approved_at' => 'datetime',
        'alert_sent' => 'boolean',
        'alert_sent_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Break type constants
    public const TYPE_LUNCH = 'lunch';

    public const TYPE_TEA = 'tea';

    public const TYPE_PERSONAL = 'personal';

    public const TYPE_EMERGENCY = 'emergency';

    public const TYPE_OTHER = 'other';

    // Status constants
    public const STATUS_ONGOING = 'ongoing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_DISPUTED = 'disputed';

    /**
     * Relationships
     */
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    /**
     * Scopes
     */
    public function scopeForUser(Builder $query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForDate(Builder $query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForDateRange(Builder $query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeOngoing(Builder $query)
    {
        return $query->where('status', self::STATUS_ONGOING);
    }

    public function scopeCompleted(Builder $query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopePaid(Builder $query)
    {
        return $query->where('is_paid', true);
    }

    public function scopeUnpaid(Builder $query)
    {
        return $query->where('is_paid', false);
    }

    public function scopeByType(Builder $query, $type)
    {
        return $query->where('break_type', $type);
    }

    public function scopeExceededLimit(Builder $query)
    {
        return $query->where('exceeded_limit', true);
    }

    public function scopeRequiresApproval(Builder $query)
    {
        return $query->where('requires_approval', true)
            ->whereNull('approved_at');
    }

    /**
     * Accessors & Mutators
     */
    public function getIsOngoingAttribute()
    {
        return $this->status === self::STATUS_ONGOING;
    }

    public function getIsCompletedAttribute()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function getFormattedDurationAttribute()
    {
        if (! $this->duration) {
            return null;
        }

        $minutes = (int) $this->duration;
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0) {
            return sprintf('%d hr %d min', $hours, $remainingMinutes);
        }

        return sprintf('%d min', $minutes);
    }

    public function getStartTimeOnlyAttribute()
    {
        return $this->start_time ? $this->start_time->format('H:i:s') : null;
    }

    public function getEndTimeOnlyAttribute()
    {
        return $this->end_time ? $this->end_time->format('H:i:s') : null;
    }

    public function getStartLocationInfoAttribute()
    {
        if ($this->start_location) {
            return $this->start_location;
        }

        if ($this->start_latitude && $this->start_longitude) {
            return sprintf('%.6f, %.6f', $this->start_latitude, $this->start_longitude);
        }

        return null;
    }

    public function getEndLocationInfoAttribute()
    {
        if ($this->end_location) {
            return $this->end_location;
        }

        if ($this->end_latitude && $this->end_longitude) {
            return sprintf('%.6f, %.6f', $this->end_latitude, $this->end_longitude);
        }

        return null;
    }

    /**
     * Helper Methods
     */
    public function start($time = null, $location = null)
    {
        $this->start_time = $time ?: now();
        $this->status = self::STATUS_ONGOING;

        if ($location) {
            $this->start_latitude = $location['latitude'] ?? null;
            $this->start_longitude = $location['longitude'] ?? null;
            $this->start_location = $location['address'] ?? null;
        }

        $this->save();

        return $this;
    }

    public function end($time = null, $location = null)
    {
        if ($this->status !== self::STATUS_ONGOING) {
            return $this;
        }

        $this->end_time = $time ?: now();
        $this->status = self::STATUS_COMPLETED;
        $this->calculateDuration();

        if ($location) {
            $this->end_latitude = $location['latitude'] ?? null;
            $this->end_longitude = $location['longitude'] ?? null;
            $this->end_location = $location['address'] ?? null;
        }

        // Check if break exceeded scheduled duration
        if ($this->scheduled_duration && $this->duration > $this->scheduled_duration) {
            $this->exceeded_limit = true;
            $this->sendExceededAlert();
        }

        $this->save();

        // Update attendance record's break hours
        if ($this->attendance) {
            $this->attendance->calculateHours();
            $this->attendance->save();
        }

        return $this;
    }

    public function cancel($reason = null)
    {
        $this->status = self::STATUS_CANCELLED;

        if ($reason) {
            $this->notes = $reason;
        }

        if ($this->status === self::STATUS_ONGOING && ! $this->end_time) {
            $this->end_time = now();
            $this->calculateDuration();
        }

        $this->save();

        return $this;
    }

    public function calculateDuration()
    {
        if ($this->start_time && $this->end_time) {
            $this->duration = $this->start_time->diffInMinutes($this->end_time);
        }

        return $this->duration;
    }

    public function approve($userId, $notes = null)
    {
        $this->approved_by_id = $userId;
        $this->approved_at = now();
        $this->approval_notes = $notes;
        $this->requires_approval = false;
        $this->save();

        return $this;
    }

    public function validate()
    {
        $errors = [];

        // Check if break duration is within limits
        if ($this->scheduled_duration && $this->duration > $this->scheduled_duration) {
            $errors[] = 'Break duration exceeded scheduled limit';
        }

        // Check if break was taken at scheduled time
        if ($this->is_scheduled && $this->start_time) {
            // Add logic to check against scheduled break times
        }

        if (! empty($errors)) {
            $this->is_valid = false;
            $this->validation_error = implode(', ', $errors);
            $this->requires_approval = true;
        } else {
            $this->is_valid = true;
            $this->validation_error = null;
        }

        $this->save();

        return $this->is_valid;
    }

    protected function sendExceededAlert()
    {
        if ($this->alert_sent) {
            return;
        }

        // Send notification to manager
        // Implementation depends on notification system

        $this->alert_sent = true;
        $this->alert_sent_at = now();
        $this->save();
    }

    /**
     * Static Methods
     */
    public static function startBreak($attendanceId, $userId, $type = self::TYPE_OTHER, array $data = [])
    {
        return self::create(array_merge([
            'attendance_id' => $attendanceId,
            'user_id' => $userId,
            'date' => Carbon::today(),
            'start_time' => now(),
            'break_type' => $type,
            'status' => self::STATUS_ONGOING,
        ], $data));
    }

    public static function getTotalBreakMinutes($attendanceId)
    {
        return self::where('attendance_id', $attendanceId)
            ->where('status', self::STATUS_COMPLETED)
            ->sum('duration');
    }

    public static function getOngoingBreak($userId)
    {
        return self::where('user_id', $userId)
            ->where('status', self::STATUS_ONGOING)
            ->latest()
            ->first();
    }

    public static function getTodayBreaks($userId)
    {
        return self::where('user_id', $userId)
            ->whereDate('date', Carbon::today())
            ->orderBy('start_time', 'desc')
            ->get();
    }
}
