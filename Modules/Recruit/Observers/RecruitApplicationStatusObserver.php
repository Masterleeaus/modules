<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitApplicationStatus;

class RecruitApplicationStatusObserver
{
    public function creating(RecruitApplicationStatus $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }
}
