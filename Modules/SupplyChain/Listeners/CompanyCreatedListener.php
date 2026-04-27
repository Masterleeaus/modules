<?php

namespace Modules\SupplyChain\Listeners;
use Modules\SupplyChain\Entities\Supplier;

class CompanyCreatedListener
{

    public function handle($event)
    {
        $company = $event->company;
        Supplier::addModuleSetting($company);
    }

}
