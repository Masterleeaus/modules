<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitJobType;

class RecruitJobTypesObserver
{
    public function creating(RecruitJobType $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }
}
