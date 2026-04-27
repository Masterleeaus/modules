<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitFooterLink;

class RecruitFooterLinkObserver
{
    public function creating(RecruitFooterLink $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }
}
