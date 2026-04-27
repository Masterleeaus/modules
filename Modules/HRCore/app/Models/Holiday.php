<?php

namespace Modules\HRCore\app\Models;

use App\Traits\UserActionsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Holiday extends Model implements AuditableContract
{
    use Auditable, SoftDeletes, UserActionsTrait;

    protected $table = 'holidays';

    protected $fillable = [
        'name',
        'date',
        'code',
        'year',
        'day',
        'type',
        'category',
        'is_optional',
        'is_restricted',
        'is_recurring',
        'applicable_for',
        'departments',
        'locations',
        'employee_types',
        'branches',
        'specific_employees',
        'description',
        'notes',
        'image',
        'color',
        'sort_order',
        'is_compensatory',
        'compensatory_date',
        'is_half_day',
        'half_day_type',
        'half_day_start_time',
        'half_day_end_time',
        'is_active',
        'is_visible_to_employees',
        'send_notification',
        'notification_days_before',
        'approved_by_id',
        'approved_at',
        'tenant_id',
        'created_by_id',
        'updated_by_id',
    ];

    protected $casts = [
        'date' => 'date',
        'compensatory_date' => 'date',
        'approved_at' => 'datetime',
        'is_optional' => 'boolean',
        'is_restricted' => 'boolean',
        'is_recurring' => 'boolean',
        'is_compensatory' => 'boolean',
        'is_half_day' => 'boolean',
        'is_active' => 'boolean',
        'is_visible_to_employees' => 'boolean',
        'send_notification' => 'boolean',
        'departments' => 'array',
        'locations' => 'array',
        'employee_types' => 'array',
        'branches' => 'array',
        'specific_employees' => 'array',
    ];

    /**
     * Get the user who approved the holiday
     */
    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by_id');
    }

    /**
     * Scope to get active holidays
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get holidays visible to employees
     */
    public function scopeVisibleToEmployees($query)
    {
        return $query->where('is_visible_to_employees', true);
    }

    /**
     * Scope to get holidays for a specific year
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope to get upcoming holidays
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString())
            ->orderBy('date', 'asc');
    }

    /**
     * Check if holiday is applicable for a specific user
     */
    public function isApplicableFor($user)
    {
        if ($this->applicable_for === 'all') {
            return true;
        }

        if ($this->applicable_for === 'department' && $this->departments) {
            return in_array($user->department_id, $this->departments);
        }

        if ($this->applicable_for === 'location' && $this->locations) {
            return in_array($user->location, $this->locations);
        }

        if ($this->applicable_for === 'employee_type' && $this->employee_types) {
            return in_array($user->employee_type, $this->employee_types);
        }

        if ($this->applicable_for === 'custom' && $this->specific_employees) {
            return in_array($user->id, $this->specific_employees);
        }

        return false;
    }

    /**
     * Automatically set year and day when date is set
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($holiday) {
            if ($holiday->date) {
                $holiday->year = $holiday->date->year;
                $holiday->day = $holiday->date->format('l'); // Day name (Monday, Tuesday, etc.)
            }
        });
    }
}
