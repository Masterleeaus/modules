<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecruitJobType extends BaseModel
{
    use HasFactory, HasCompany;

    protected $fillable = ['job_type', 'company_id'];

    protected $table = 'recruit_job_types';
}
