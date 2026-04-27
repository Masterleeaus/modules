<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitJobOfferLetter;

class RecruitJobOfferLetterObserver
{
    public function creating(RecruitJobOfferLetter $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }
}
