<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitSkill;

class RecruitSkillObserver
{
    public function creating(RecruitSkill $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }
}
