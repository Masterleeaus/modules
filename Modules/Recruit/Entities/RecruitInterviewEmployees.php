<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Models\User;
use App\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Recruit\Observers\InterviewEmployeeObserver;

class RecruitInterviewEmployees extends BaseModel
{
    protected $fillable = ['user_id', 'recruit_interview_schedule_id'];

    public static function boot()
    {
        parent::boot();
        static::observe(InterviewEmployeeObserver::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(RecruitInterviewSchedule::class, 'recruit_interview_schedule_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScope(ActiveScope::class);
    }

}
