<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitCandidateDatabase;

class RecruitCandidateDatabaseObserver
{
    public function creating(RecruitCandidateDatabase $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }
}
