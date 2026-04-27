<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitEmailNotificationSetting;

class RecruitEmailNotificationObserver
{
    public function creating(RecruitEmailNotificationSetting $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }
}
