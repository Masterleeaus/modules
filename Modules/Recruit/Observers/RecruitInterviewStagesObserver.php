<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitInterviewStage;

class RecruitInterviewStagesObserver
{
    public function creating(RecruitInterviewStage $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }
}
