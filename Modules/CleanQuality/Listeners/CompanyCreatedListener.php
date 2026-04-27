<?php

namespace Modules\CleanQuality\Listeners;

use Modules\CleanQuality\Entities\RecurringSchedule;

class CompanyCreatedListener
{

    public function handle($event)
    {
        $company = $event->company;
        RecurringSchedule::addModuleSetting($company);
    }

}
