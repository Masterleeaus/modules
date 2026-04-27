<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecruitApplicantNote extends BaseModel
{
    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(RecruitJobApplication::class, 'recruit_job_application_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
