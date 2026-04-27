<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitSelectedSalaryComponent;

class RecruitSelectedSalaryComponentObserver
{
    public function creating(RecruitSelectedSalaryComponent $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }
}
