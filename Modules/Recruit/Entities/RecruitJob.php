<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Models\CompanyAddress;
use App\Models\Currency;
use App\Models\Team;
use App\Models\User;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecruitJob extends BaseModel
{
    use SoftDeletes, HasCompany;

    protected $dates = ['end_date', 'start_date', 'deleted_at'];

    protected $casts = [
        'meta_details' => 'array',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'department_id');
    }

    public function skills(): HasMany
    {
        return $this->hasMany(RecruitJobSkill::class, 'recruit_job_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(RecruitJobApplication::class, 'recruit_job_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(CompanyAddress::class, 'location_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(RecruitJobFile::class, 'recruit_job_id')->orderByDesc('id');
    }

    public function workExperience(): BelongsTo
    {
        return $this->belongsTo(RecruitWorkExperience::class, 'recruit_work_experience_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recruiter_id');
    }

    public function jobType(): BelongsTo
    {
        return $this->belongsTo(RecruitJobType::class, 'recruit_job_type_id');
    }

    public function address(): BelongsToMany
    {
        return $this->belongsToMany(CompanyAddress::class, 'recruit_job_addresses', 'recruit_job_id', 'company_address_id', 'id', 'id');
    }

    public function recruiter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recruiter_id');
    }

    public static function activeJobs()
    {
        return RecruitJob::where('status', 'open')
            ->where('start_date', '<=', now()->format('Y-m-d'))
            ->where('end_date', '>=', now()->format('Y-m-d'))
            ->get();
    }

    public function stages(): BelongsToMany
    {
        return $this->belongsToMany(RecruitInterviewStage::class, 'job_interview_stages', 'recruit_job_id', 'recruit_interview_stage_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(RecruitJobCategory::class, 'recruit_job_category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(RecruitJobSubCategory::class, 'recruit_job_sub_category_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function question(): belongsToMany
    {
        return $this->belongsToMany(RecruitCustomQuestion::class, 'recruit_job_questions');
    }
}
