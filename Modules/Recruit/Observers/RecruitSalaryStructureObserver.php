<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitSalaryStructure;

class RecruitSalaryStructureObserver
{
    public function creating(RecruitSalaryStructure $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }
}
