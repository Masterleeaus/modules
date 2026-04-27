<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;

class RecruitApplicationSkill extends BaseModel
{
    protected $dates = ['end_date', 'start_date'];

    protected $table = 'recruit_application_skills';
}
