<?php

namespace Modules\Accountings\Listeners;

use Modules\Accountings\Entities\Accounting;

class CompanyCreatedListener
{

    public function handle($event)
    {
        $company = $event->company;
        Accounting::addModuleSetting($company);
    }

}
