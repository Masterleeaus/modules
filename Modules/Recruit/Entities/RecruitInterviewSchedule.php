<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Models\EmployeeDetails;
use App\Models\User;
use App\Traits\HasCompany;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Modules\Zoom\Entities\ZoomMeeting;

class RecruitInterviewSchedule extends BaseModel
{

    use Notifiable, HasCompany;

    protected $dates = ['end_date', 'start_date', 'schedule_date'];

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(RecruitJobApplication::class, 'recruit_job_application_id')->withTrashed();
    }

    // Relation with user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function meeting(): BelongsTo
    {
        if (module_enabled('Zoom')) {
            return $this->belongsTo(ZoomMeeting::class, 'meeting_id');
        }
    }

    // Relation with comment
    public function comments(): HasMany
    {
        return $this->hasMany(RecruitInterviewComments::class, 'recruit_interview_schedule_id', 'id');
    }

    // Relation with employee
    public function employeesData(): HasMany
    {
        return $this->hasMany(RecruitInterviewEmployees::class, 'recruit_interview_schedule_id', 'id');
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, RecruitInterviewEmployees::class, 'recruit_interview_schedule_id');
    }

    public function employeeData($userId)
    {
        return RecruitInterviewSchedule::where('user_id', $userId)->where('recruit_interview_schedule_id', $this->id)->first();
    }

    public function files(): HasMany
    {
        return $this->hasMany(RecruitInterviewFile::class, 'recruit_interview_schedule_id')->orderByDesc('id');
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'recruit_interview_employees', 'recruit_interview_schedule_id', 'user_id');
    }

    public function recruiters(): BelongsToMany
    {
        return $this->belongsToMany(EmployeeDetails::class, 'users', 'user_id', 'id');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(RecruitInterviewStage::class, 'recruit_interview_stage_id');
    }

    public function evaluation(): HasMany
    {
        return $this->hasMany(RecruitInterviewEvaluation::class, 'recruit_interview_schedule_id');
    }

}
