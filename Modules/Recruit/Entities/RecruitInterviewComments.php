<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecruitInterviewComments extends BaseModel
{
    protected $guarded = ['id'];

    protected $dates = ['created_at'];

    protected $table = 'recruit_interview_comments';

    // Relation with job application
    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(RecruitInterviewSchedule::class);
    }

    // Relation with user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
