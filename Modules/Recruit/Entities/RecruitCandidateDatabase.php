<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecruitCandidateDatabase extends BaseModel
{
    use HasFactory, HasCompany;

    protected $table = 'recruit_candidate_database';

    protected $casts = [
        'skills' => 'json',
    ];

    protected $fillable = [];

    public function job(): BelongsTo
    {
        return $this->belongsTo(RecruitJob::class, 'recruit_job_id')->withTrashed();
    }
}
