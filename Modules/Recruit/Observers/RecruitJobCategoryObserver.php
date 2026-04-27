<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitJobCategory;

class RecruitJobCategoryObserver
{
    public function creating(RecruitJobCategory $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }
}
