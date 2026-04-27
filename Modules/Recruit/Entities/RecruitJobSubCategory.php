<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecruitJobSubCategory extends BaseModel
{
    use HasFactory, HasCompany;

    protected $fillable = [];

    protected $table = 'recruit_job_sub_categories';

    public function category(): BelongsTo
    {
        return $this->belongsTo(RecruitJobCategory::class, 'recruit_job_category_id');
    }
}
