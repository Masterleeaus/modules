<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitInterviewEvaluation;

class RecruitInterviewEvaluationObserver
{
    public function creating(RecruitInterviewEvaluation $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }
}
