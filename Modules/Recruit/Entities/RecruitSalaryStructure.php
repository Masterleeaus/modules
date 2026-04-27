<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;

class RecruitSalaryStructure extends BaseModel
{
    use HasCompany;

    protected $fillable = [];
}
