<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitWorkExperience;

class RecruitWorkExperienceObserver
{
    public function creating(RecruitWorkExperience $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }
}
