<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Models\EmployeeDetails;
use App\Models\User;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Recruit\Observers\RecruiterObserver;

class Recruiter extends BaseModel
{
    use HasCompany;

    public static function boot()
    {
        parent::boot();
        static::observe(RecruiterObserver::class);
    }

    protected $fillable = [];

    public function employeeDetail(): BelongsTo
    {
        return $this->belongsTo(EmployeeDetails::class, 'employee_id');
    }

    public function emp(): BelongsTo
    {
        return $this->belongsTo(RecruitInterviewEmployees::class, 'employee_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
