<?php

namespace Modules\Purchase\Console;

use App\Models\Company;
use Illuminate\Console\Command;
use Modules\Purchase\Entities\PurchaseSetting;

class ActivateModuleCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'purchase:activate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add all the module settings of purchase module';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $companies = Company::get();

        foreach ($companies as $company) {
            PurchaseSetting::addModuleSetting($company);
        }
    }

}
