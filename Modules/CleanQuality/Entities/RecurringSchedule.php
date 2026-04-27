<?php

namespace Modules\CleanQuality\Entities;

use Carbon\Carbon;
use App\Models\User;
use App\Models\BaseModel;
use App\Traits\HasCompany;
use App\Models\ModuleSetting;
use Illuminate\Notifications\Notifiable;
use Modules\CleanQuality\Entities\Schedule;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringSchedule extends BaseModel
{
    use Notifiable, HasCompany;

    protected $table = 'inspection_schedule_recurring';
    protected $dates = ['issue_date', 'next_schedule_date'];

    const MODULE_NAME = 'quality_control';
    const ROTATION_COLOR = [
        'daily' => 'success',
        'weekly' => 'info',
        'bi-weekly' => 'warning',
        'monthly' => 'secondary',
        'quarterly' => 'light',
        'half-yearly' => 'dark',
        'annually' => 'success',
    ];

    public static function addModuleSetting($company)
    {
        // create admin, employee and client module settings
        $roles = ['admin', 'employee'];

        if (class_exists(ModuleSetting::class)) {
            ModuleSetting::createRoleSettingEntry(self::MODULE_NAME, $roles, $company);
        }

    }

    public function recurrings(): HasMany
    {
        return $this->hasMany(Schedule::class, 'schedule_recurring_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RecurringScheduleItems::class, 'schedule_recurring_id');
    }

    public function floor(): BelongsTo
    {
        $related = class_exists('Modules\Units\Entities\Floor')
            ? 'Modules\Units\Entities\Floor'
            : User::class;

        return $this->belongsTo($related, 'floor_id');
    }

    public function tower(): BelongsTo
    {
        $related = class_exists('Modules\Units\Entities\Tower')
            ? 'Modules\Units\Entities\Tower'
            : User::class;

        return $this->belongsTo($related, 'tower_id');
    }

    public function getIssueOnAttribute()
    {
        if (is_null($this->issue_date)) {
            return '';
        }

        return Carbon::parse($this->issue_date)->format('d F, Y');

    }



}
