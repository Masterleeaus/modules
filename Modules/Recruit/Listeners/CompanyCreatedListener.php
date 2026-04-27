<?php

namespace Modules\Recruit\Listeners;

use Modules\Recruit\Entities\RecruitSetting;

class CompanyCreatedListener
{

    public function handle($event)
    {
        $company = $event->company;
        RecruitSetting::addModuleSetting($company);
    }
}
