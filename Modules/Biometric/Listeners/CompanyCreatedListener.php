<?php

namespace Modules\Biometric\Listeners;

use Modules\Biometric\Entities\BiometricGlobalSetting;
use Modules\Biometric\Entities\BiometricSetting;

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

        $bio = new BiometricSetting();
        $bio->company_id = $company->id;
        $bio->save();

        BiometricGlobalSetting::addModuleSetting($company);
    }

}
