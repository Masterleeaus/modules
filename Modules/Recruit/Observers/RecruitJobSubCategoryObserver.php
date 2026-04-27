<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitJobSubCategory;

class RecruitJobSubCategoryObserver
{
    public function creating(RecruitJobSubCategory $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }
}
