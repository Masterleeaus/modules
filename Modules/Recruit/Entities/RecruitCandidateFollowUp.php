<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Modules\Recruit\Observers\RecruitCandidateFollowUpObserver;

class RecruitCandidateFollowUp extends BaseModel
{
    use Notifiable;

    protected $fillable = [];

    protected $table = 'recruit_candidate_follow_ups';

    protected $dates = ['next_follow_up_date', 'created_at'];

    public static function boot()
    {
        parent::boot();
        static::observe(RecruitCandidateFollowUpObserver::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(RecruitJobApplication::class, 'recruit_job_application_id');
    }
}
