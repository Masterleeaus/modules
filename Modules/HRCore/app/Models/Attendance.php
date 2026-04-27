<?php

namespace Modules\HRCore\app\Models;

use App\Models\User;
use App\Traits\UserActionsTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\FieldManager\app\Models\Activity;
use Modules\FieldManager\app\Models\Visit;

class Attendance extends Model
{
    use HasFactory, SoftDeletes, UserActionsTrait;

    protected $table = 'attendances';

    protected $fillable = [
        'user_id',
        'date',
        'check_in_time',
        'check_out_time',
        'shift_id',
        'working_hours',
        'break_hours',
        'overtime_hours',
        'late_hours',
        'early_hours',
        'status',
        'is_holiday',
        'is_weekend',
        'is_half_day',
        'notes',
        'approved_by_id',
        'approved_at',
        'created_by_id',
        'updated_by_id',
        'tenant_id',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'approved_at' => 'datetime',
        'working_hours' => 'decimal:2',
        'break_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'late_hours' => 'decimal:2',
        'early_hours' => 'decimal:2',
        'is_holiday' => 'boolean',
        'is_weekend' => 'boolean',
        'is_half_day' => 'boolean',
    ];

    // Status constants
    public const STATUS_CHECKED_IN = 'checked_in';

    public const STATUS_CHECKED_OUT = 'checked_out';

    public const STATUS_ABSENT = 'absent';

    public const STATUS_LEAVE = 'leave';

    public const STATUS_HOLIDAY = 'holiday';

    public const STATUS_WEEKEND = 'weekend';

    public const STATUS_HALF_DAY = 'half_day';

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function breaks()
    {
        return $this->hasMany(AttendanceBreak::class);
    }

    public function regularization()
    {
        return $this->hasOne(AttendanceRegularization::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
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

    public function scopePresent(Builder $query)
    {
        return $query->whereIn('status', [
            self::STATUS_CHECKED_IN,
            self::STATUS_CHECKED_OUT,
            self::STATUS_HALF_DAY,
        ]);
    }

    public function scopeAbsent(Builder $query)
    {
        return $query->where('status', self::STATUS_ABSENT);
    }

    public function scopeLate(Builder $query)
    {
        return $query->whereHas('shift', function ($q) {
            $q->whereRaw('TIME(check_in_time) > TIME(shifts.start_time)');
        });
    }

    public function scopeEarlyCheckout(Builder $query)
    {
        return $query->whereHas('shift', function ($q) {
            $q->whereRaw('TIME(check_out_time) < TIME(shifts.end_time)');
        });
    }

    /**
     * Accessors & Mutators
     */
    public function getIsCheckedInAttribute()
    {
        return $this->status === self::STATUS_CHECKED_IN;
    }

    public function getIsCheckedOutAttribute()
    {
        return $this->status === self::STATUS_CHECKED_OUT;
    }

    public function getIsPresentAttribute()
    {
        return in_array($this->status, [
            self::STATUS_CHECKED_IN,
            self::STATUS_CHECKED_OUT,
            self::STATUS_HALF_DAY,
        ]);
    }

    public function getIsAbsentAttribute()
    {
        return $this->status === self::STATUS_ABSENT;
    }

    public function getIsOnLeaveAttribute()
    {
        return $this->status === self::STATUS_LEAVE;
    }

    public function getFormattedWorkingHoursAttribute()
    {
        $hours = floor($this->working_hours);
        $minutes = round(($this->working_hours - $hours) * 60);

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public function getFormattedBreakHoursAttribute()
    {
        $hours = floor($this->break_hours);
        $minutes = round(($this->break_hours - $hours) * 60);

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public function getLateMinutesAttribute()
    {
        if (! $this->shift || ! $this->check_in_time) {
            return 0;
        }

        // Get the date string
        $dateStr = $this->date instanceof Carbon ? $this->date->format('Y-m-d') : (string) $this->date;
        $dateStr = trim(explode(' ', $dateStr)[0]);

        // Get shift start time (extract time part only if it includes date)
        $shiftStartTime = $this->shift->start_time;
        if (strpos($shiftStartTime, ' ') !== false) {
            $shiftStartTime = explode(' ', $shiftStartTime)[1];
        }

        $shiftStart = Carbon::parse($dateStr.' '.$shiftStartTime);
        $checkIn = $this->check_in_time;

        if ($checkIn->gt($shiftStart)) {
            return $checkIn->diffInMinutes($shiftStart);
        }

        return 0;
    }

    public function getEarlyCheckoutMinutesAttribute()
    {
        if (! $this->shift || ! $this->check_out_time) {
            return 0;
        }

        // Get the date string
        $dateStr = $this->date instanceof Carbon ? $this->date->format('Y-m-d') : (string) $this->date;
        $dateStr = trim(explode(' ', $dateStr)[0]);

        // Get shift end time (extract time part only if it includes date)
        $shiftEndTime = $this->shift->end_time;
        if (strpos($shiftEndTime, ' ') !== false) {
            $shiftEndTime = explode(' ', $shiftEndTime)[1];
        }

        $shiftEnd = Carbon::parse($dateStr.' '.$shiftEndTime);
        $checkOut = $this->check_out_time;

        if ($checkOut->lt($shiftEnd)) {
            return $shiftEnd->diffInMinutes($checkOut);
        }

        return 0;
    }

    /**
     * Helper Methods
     */
    public function isCheckedIn(): bool
    {
        return $this->status === self::STATUS_CHECKED_IN;
    }

    public function isCheckedOut(): bool
    {
        return $this->status === self::STATUS_CHECKED_OUT;
    }

    public function canCheckIn(): bool
    {
        return in_array($this->status, [self::STATUS_ABSENT, null]);
    }

    public function canCheckOut(): bool
    {
        return $this->status === self::STATUS_CHECKED_IN;
    }

    public function checkIn($time = null, $logData = [])
    {
        $this->check_in_time = $time ?: now();
        $this->status = self::STATUS_CHECKED_IN;

        // Calculate late hours if shift is defined
        if ($this->shift) {
            $lateMinutes = $this->getLateMinutesAttribute();
            $this->late_hours = round($lateMinutes / 60, 2);
        }

        $this->save();

        // Create attendance log
        $this->attendanceLogs()->create(array_merge([
            'user_id' => $this->user_id,
            'date' => $this->date,
            'time' => $this->check_in_time->format('H:i:s'),
            'logged_at' => $this->check_in_time,
            'type' => 'check_in',
            'shift_id' => $this->shift_id,
        ], $logData));

        return $this;
    }

    public function checkOut($time = null, $logData = [])
    {
        $this->check_out_time = $time ?: now();
        $this->status = self::STATUS_CHECKED_OUT;
        $this->calculateHours();
        $this->save();

        // Create attendance log
        $this->attendanceLogs()->create(array_merge([
            'user_id' => $this->user_id,
            'date' => $this->date,
            'time' => $this->check_out_time->format('H:i:s'),
            'logged_at' => $this->check_out_time,
            'type' => 'check_out',
            'shift_id' => $this->shift_id,
        ], $logData));

        return $this;
    }

    public function calculateHours()
    {
        if (! $this->check_in_time || ! $this->check_out_time) {
            return;
        }

        // Calculate total hours
        $totalMinutes = $this->check_in_time->diffInMinutes($this->check_out_time);

        // Calculate break hours
        $breakMinutes = $this->breaks()
            ->whereNotNull('end_time')
            ->get()
            ->sum(function ($break) {
                return $break->start_time->diffInMinutes($break->end_time);
            });

        // Calculate working hours
        $workingMinutes = $totalMinutes - $breakMinutes;
        $this->working_hours = round($workingMinutes / 60, 2);
        $this->break_hours = round($breakMinutes / 60, 2);

        // Calculate late and early hours if shift is defined
        if ($this->shift) {
            // Calculate late hours
            $lateMinutes = $this->getLateMinutesAttribute();
            $this->late_hours = round($lateMinutes / 60, 2);

            // Calculate early checkout hours
            $earlyMinutes = $this->getEarlyCheckoutMinutesAttribute();
            $this->early_hours = round($earlyMinutes / 60, 2);

            // Calculate overtime
            $shiftHours = $this->shift->working_hours ?? 8;
            if ($this->working_hours > $shiftHours) {
                $this->overtime_hours = round($this->working_hours - $shiftHours, 2);
            }
        }
    }

    public function startBreak($type = 'other', $logData = [])
    {
        $break = $this->breaks()->create([
            'user_id' => $this->user_id,
            'date' => $this->date,
            'start_time' => now(),
            'break_type' => $type,
            'status' => 'ongoing',
        ]);

        // Create attendance log
        $this->attendanceLogs()->create(array_merge([
            'user_id' => $this->user_id,
            'date' => $this->date,
            'time' => now()->format('H:i:s'),
            'logged_at' => now(),
            'type' => 'break_start',
            'break_type' => $type,
            'shift_id' => $this->shift_id,
        ], $logData));

        return $break;
    }

    public function endBreak($breakId = null, $logData = [])
    {
        $query = $this->breaks()->where('status', 'ongoing');

        if ($breakId) {
            $query->where('id', $breakId);
        }

        $break = $query->first();

        if ($break) {
            $break->end_time = now();
            $break->status = 'completed';
            $break->duration = $break->start_time->diffInMinutes($break->end_time);
            $break->save();

            // Create attendance log
            $this->attendanceLogs()->create(array_merge([
                'user_id' => $this->user_id,
                'date' => $this->date,
                'time' => now()->format('H:i:s'),
                'logged_at' => now(),
                'type' => 'break_end',
                'break_type' => $break->break_type,
                'break_duration' => $break->duration,
                'shift_id' => $this->shift_id,
            ], $logData));

            // Recalculate hours
            $this->calculateHours();
            $this->save();
        }

        return $break;
    }

    public function getLatestLog($type = null)
    {
        $query = $this->attendanceLogs()->latest('logged_at');

        if ($type) {
            $query->where('type', $type);
        }

        return $query->first();
    }

    public function getCheckInLog()
    {
        return $this->attendanceLogs()
            ->where('type', 'check_in')
            ->latest('logged_at')
            ->first();
    }

    public function getCheckOutLog()
    {
        return $this->attendanceLogs()
            ->where('type', 'check_out')
            ->latest('logged_at')
            ->first();
    }

    public function hasOngoingBreak()
    {
        return $this->breaks()
            ->where('status', 'ongoing')
            ->exists();
    }

    public function getOngoingBreak()
    {
        return $this->breaks()
            ->where('status', 'ongoing')
            ->first();
    }

    /**
     * Static Methods
     */
    public static function getOrCreateForToday($userId)
    {
        return self::firstOrCreate(
            [
                'user_id' => $userId,
                'date' => Carbon::today(),
            ],
            [
                'status' => self::STATUS_ABSENT,
                'shift_id' => User::find($userId)->shift_id ?? null,
            ]
        );
    }

    public static function getTodayAttendance($userId)
    {
        return self::where('user_id', $userId)
            ->whereDate('date', Carbon::today())
            ->first();
    }
}
