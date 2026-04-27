<?php

namespace Modules\Biometric\Console;

use App\Models\Company;
use App\Scopes\ActiveScope;
use Illuminate\Console\Command;
use Modules\Biometric\Entities\BiometricGlobalSetting;
use Modules\Biometric\Entities\BiometricSetting;

class ActivateModuleCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'biometric:activate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add all the module settings of asset module';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $companies = Company::withoutGlobalScopes([ActiveScope::class])->get();

        foreach ($companies as $company) {

            $bio = new BiometricSetting();
            $bio->company_id = $company->id;
            $bio->save();

            BiometricGlobalSetting::addModuleSetting($company);
        }
    }
}
