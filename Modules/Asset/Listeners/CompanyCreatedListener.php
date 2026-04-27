<?php

namespace Modules\Asset\Listeners;

use Modules\Asset\Entities\AssetSetting;

class CompanyCreatedListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $company = $event->company;
        AssetSetting::addModuleSetting($company);
    }
}
