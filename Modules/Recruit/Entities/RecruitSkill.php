<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RecruitSkill extends BaseModel
{
    use HasCompany;

    protected $fillable = ['name'];

    public function job(): BelongsTo
    {
        return $this->belongsTo(RecruitJob::class, 'recruit_job_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(RecruitJob::class, 'recruit_job_skills');
    }
}
