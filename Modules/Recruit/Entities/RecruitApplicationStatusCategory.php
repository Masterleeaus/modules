<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecruitApplicationStatusCategory extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name', 'company_id'];

    public function status(): HasMany
    {
        return $this->hasMany(RecruitApplicationStatus::class, 'recruit_application_status_category_id');
    }
}
