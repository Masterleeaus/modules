<?php

namespace Modules\Purchase\Listeners;

use Modules\Purchase\Entities\PurchaseSetting;

class CompanyCreatedListener
{

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $company = $event->company;
        PurchaseSetting::addModuleSetting($company);
    }

}
