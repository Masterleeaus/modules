<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Models\User;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Recruit\Observers\EvaluationObserver;

class RecruitInterviewEvaluation extends BaseModel
{
    use HasCompany;

    protected $fillable = [];

    public static function boot()
    {
        parent::boot();
        static::observe(EvaluationObserver::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(RecruitRecommendationStatus::class, 'recruit_recommendation_status_id');
    }

    public function interview(): BelongsTo
    {
        return $this->belongsTo(RecruitInterviewSchedule::class, 'recruit_interview_schedule_id');
    }

    // Relation with user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(RecruitInterviewStage::class, 'recruit_interview_stage_id');
    }
}
