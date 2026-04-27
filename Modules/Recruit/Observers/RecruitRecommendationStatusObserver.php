<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitRecommendationStatus;

class RecruitRecommendationStatusObserver
{
    public function creating(RecruitRecommendationStatus $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }
}
