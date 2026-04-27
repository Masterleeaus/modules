<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitCustomQuestion;

class RecruitCustomQuestionObserver
{
    public function creating(RecruitCustomQuestion $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }
}
