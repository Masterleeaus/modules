<?php

namespace Modules\Recruit\Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Modules\Recruit\Entities\RecruitCandidateDatabase;
use Modules\Recruit\Entities\RecruitJobApplication;

class CandidateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($companyId)
    {
        $faker = Factory::create();
        $application = RecruitJobApplication::with('job')
            ->where('company_id', $companyId)
            ->get();

        foreach ($application as $app) {
            $letters = [
                [
                    'name' => $app->full_name,
                    'recruit_job_id' => $app->job->id,
                    'location_id' => 1,
                    'Job_applied_on' => now()->subDays('30')->format('Y-m-d H:i:s'),
                    'skills' => [1],
                    'job_application_id' => $app->id,
                ],
            ];

            foreach ($letters as $letter) {
                $letter['company_id'] = $companyId;
                RecruitCandidateDatabase::create($letter);
            }
        }
    }
}
