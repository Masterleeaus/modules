<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\Recruiter;

class RecruiterObserver
{
    public function creating(Recruiter $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }
}
