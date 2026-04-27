<?php

namespace Modules\Complaint\Listeners;
use Modules\Complaint\Entities\Complaint;
use Modules\Complaint\Database\Seeders\QualityIssueDefaultsSeeder;

class CompanyCreatedListener
{

    public function handle($event)
    {
        $company = $event->company;
        Complaint::addModuleSetting($company);

        // Seed cleaning-aligned defaults (types, channels, groups, templates)
        // for the newly created tenant.
        try {
            QualityIssueDefaultsSeeder::seedForCompany($company->id);
        } catch (\Throwable $e) {
            // Non-fatal.
        }
    }

}
