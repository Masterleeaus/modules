<?php

namespace Modules\HRCore\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeHistory extends Model
{
    protected $fillable = [
        'user_id',
        'event_type',
        'old_data',
        'new_data',
        'reason',
        'remarks',
        'changed_by',
        'effective_date',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'effective_date' => 'datetime',
    ];

    /**
     * Event types
     */
    const EVENT_DESIGNATION_CHANGE = 'designation_change';

    const EVENT_TEAM_TRANSFER = 'team_transfer';

    const EVENT_SALARY_REVISION = 'salary_revision';

    const EVENT_STATUS_CHANGE = 'status_change';

    const EVENT_REPORTING_CHANGE = 'reporting_change';

    const EVENT_SHIFT_CHANGE = 'shift_change';

    const EVENT_PERSONAL_INFO_UPDATE = 'personal_info_update';

    const EVENT_WORK_INFO_UPDATE = 'work_info_update';

    const EVENT_PROBATION_UPDATE = 'probation_update';

    const EVENT_PROMOTION = 'promotion';

    const EVENT_DEMOTION = 'demotion';

    const EVENT_ONBOARDING = 'onboarding';

    const EVENT_OFFBOARDING = 'offboarding';

    const EVENT_LIFECYCLE_CHANGE = 'lifecycle_change';

    /**
     * Get the employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who made the change
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Get human-readable event type
     */
    public function getEventTypeLabel(): string
    {
        return match ($this->event_type) {
            self::EVENT_DESIGNATION_CHANGE => 'Designation Change',
            self::EVENT_TEAM_TRANSFER => 'Team Transfer',
            self::EVENT_SALARY_REVISION => 'Salary Revision',
            self::EVENT_STATUS_CHANGE => 'Status Change',
            self::EVENT_REPORTING_CHANGE => 'Reporting Manager Change',
            self::EVENT_SHIFT_CHANGE => 'Shift Change',
            self::EVENT_PERSONAL_INFO_UPDATE => 'Personal Information Update',
            self::EVENT_WORK_INFO_UPDATE => 'Work Information Update',
            self::EVENT_PROBATION_UPDATE => 'Probation Update',
            self::EVENT_PROMOTION => 'Promotion',
            self::EVENT_DEMOTION => 'Demotion',
            self::EVENT_ONBOARDING => 'Employee Onboarding',
            self::EVENT_OFFBOARDING => 'Employee Offboarding',
            self::EVENT_LIFECYCLE_CHANGE => 'Lifecycle State Change',
            default => ucwords(str_replace('_', ' ', $this->event_type)),
        };
    }

    /**
     * Create history entry for changes
     */
    public static function recordChange(User $employee, string $eventType, array $oldData, array $newData, ?string $reason = null, ?string $remarks = null): self
    {
        return self::create([
            'user_id' => $employee->id,
            'event_type' => $eventType,
            'old_data' => $oldData,
            'new_data' => $newData,
            'reason' => $reason,
            'remarks' => $remarks,
            'changed_by' => auth()->id(),
            'effective_date' => now(),
        ]);
    }

    /**
     * Get formatted change description
     */
    public function getChangeDescription(): string
    {
        $changes = [];

        if ($this->old_data && $this->new_data) {
            foreach ($this->new_data as $key => $newValue) {
                $oldValue = $this->old_data[$key] ?? null;

                if ($oldValue != $newValue) {
                    $changes[] = $this->formatChange($key, $oldValue, $newValue);
                }
            }
        }

        return implode(', ', $changes);
    }

    /**
     * Format individual change
     */
    protected function formatChange($key, $oldValue, $newValue): string
    {
        // Format based on key
        switch ($key) {
            case 'designation_id':
                $oldDesignation = \Modules\HRCore\app\Models\Designation::find($oldValue)?->name ?? 'N/A';
                $newDesignation = \Modules\HRCore\app\Models\Designation::find($newValue)?->name ?? 'N/A';

                return "Designation: {$oldDesignation} → {$newDesignation}";

            case 'team_id':
                $oldTeam = \Modules\HRCore\app\Models\Team::find($oldValue)?->name ?? 'N/A';
                $newTeam = \Modules\HRCore\app\Models\Team::find($newValue)?->name ?? 'N/A';

                return "Team: {$oldTeam} → {$newTeam}";

            case 'reporting_to_id':
                $oldManager = User::find($oldValue)?->name ?? 'N/A';
                $newManager = User::find($newValue)?->name ?? 'N/A';

                return "Reporting Manager: {$oldManager} → {$newManager}";

            case 'base_salary':
                return 'Salary: '.number_format($oldValue, 2).' → '.number_format($newValue, 2);

            case 'status':
            case 'employee_status':
                return ucfirst(str_replace('_', ' ', $key)).": {$oldValue} → {$newValue}";

            case 'state':
                return 'Lifecycle State: '.ucfirst($oldValue).' → '.ucfirst($newValue);

            default:
                return ucfirst(str_replace('_', ' ', $key)).": {$oldValue} → {$newValue}";
        }
    }
}
