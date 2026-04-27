<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RecruitApplicationStatus extends BaseModel
{

    use HasCompany;

    protected $dates = ['end_date', 'start_date'];

    protected $guarded = ['id'];

    protected $table = 'recruit_application_status';

    public function applications(): HasMany
    {
        return $this->hasMany(RecruitJobApplication::class, 'recruit_application_status_id');
    }

    public function userSetting(): HasOne
    {
        return $this->hasOne(RecruitJobboardSetting::class, 'recruit_application_status_id')->where('user_id', user()->id);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(RecruitApplicationStatusCategory::class, 'recruit_application_status_category_id');
    }

}
