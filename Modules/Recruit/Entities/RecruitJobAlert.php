<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class RecruitJobAlert extends BaseModel
{
    use HasCompany, Notifiable;

    protected $fillable = [];

    public function workExperience()
    {
        return $this->belongsTo(RecruitWorkExperience::class, 'recruit_work_experience_id');
    }
}
