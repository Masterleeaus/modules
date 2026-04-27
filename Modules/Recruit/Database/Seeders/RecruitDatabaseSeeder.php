<?php

namespace Modules\Recruit\Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class RecruitDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        config(['app.seeding' => true]);

        $companies = Company::all();

        foreach ($companies as $company) {

            if (! App::environment('codecanyon')) {
                $this->call(SkillsTableSeeder::class, false, ['companyId' => $company->id]);
                $this->call(WorkExperienceTableSeeder::class, false, ['companyId' => $company->id]);
                $this->call(JobsTableSeeder::class, false, ['companyId' => $company->id]);
                $this->call(JobApplicationsTableSeeder::class, false, ['companyId' => $company->id]);
                $this->call(InterviewsTableSeeder::class, false, ['companyId' => $company->id]);
                $this->call(OfferLettersTableSeeder::class, false, ['companyId' => $company->id]);
                $this->call(CandidateTableSeeder::class, false, ['companyId' => $company->id]);
            }

        }

        config(['app.seeding' => false]);
    }
}
