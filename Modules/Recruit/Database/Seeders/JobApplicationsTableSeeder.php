<?php

namespace Modules\Recruit\Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Modules\Recruit\Entities\ApplicationSource;
use Modules\Recruit\Entities\RecruitApplicationStatus;
use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Entities\RecruitJobApplication;

class JobApplicationsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run($companyId)
    {
        $faker = Factory::create();

        $source = ApplicationSource::pluck('id');
        $job_id = RecruitJob::where('company_id', $companyId)->pluck('id');
        $status = RecruitApplicationStatus::where('company_id', $companyId)->pluck('id');
        $user = User::where('company_id', $companyId)->pluck('id');

        $jobApps = [
            [
                'full_name' => $faker->name,
                'email' => $faker->email,
                'phone' => 9876543210,
                'date_of_birth' => Carbon::today()->subYear('20')->subDays(rand(0, 365)),
                'gender' => 'male',
                'application_source_id' => $source->random(),
                'recruit_job_id' => $job_id->random(),
                'recruit_application_status_id' => $status->random(),
                'location_id' => 1,
                'total_experience' => '1-2',
                'currenct_ctc_rate' => 'Year',
                'expected_ctc_rate' => 'Year',
                'current_location' => 'India',
                'current_ctc' => '4500',
                'currenct_ctc_rate' => 'Day',
                'expected_ctc' => '8000',
                'expected_ctc_rate' => 'Day',
                'notice_period' => '15',
                'application_sources' => 'addedByUser',
                'added_by' => $user->random()
            ],
            [
                'full_name' => $faker->name,
                'email' => $faker->email,
                'phone' => 9876543210,
                'date_of_birth' => Carbon::today()->subYear('18')->subDays(rand(0, 365)),
                'gender' => 'female',
                'application_source_id' => $source->random(),
                'recruit_job_id' => $job_id->random(),
                'recruit_application_status_id' => $status->random(),
                'location_id' => 1,
                'total_experience' => '3-4',
                'currenct_ctc_rate' => 'Year',
                'expected_ctc_rate' => 'Year',
                'current_location' => 'USA',
                'current_ctc' => '5000',
                'expected_ctc' => '7000',
                'notice_period' => '15',
                'application_sources' => 'addedByUser',
                'added_by' => $user->random()
            ],
            [
                'full_name' => $faker->name,
                'email' => $faker->email,
                'phone' => 9876543210,
                'date_of_birth' => Carbon::today()->subYear('22')->subDays(rand(0, 365)),
                'gender' => 'male',
                'application_source_id' => $source->random(),
                'recruit_job_id' => $job_id->random(),
                'recruit_application_status_id' => $status->random(),
                'location_id' => 1,
                'total_experience' => '1-2',
                'current_location' => 'India',
                'current_ctc' => '70',
                'expected_ctc' => '140',
                'currenct_ctc_rate' => 'Year',
                'expected_ctc_rate' => 'Year',
                'notice_period' => '45',
                'application_sources' => 'addedByUser',
                'added_by' => $user->random()
            ],
            [
                'full_name' => $faker->name,
                'email' => $faker->email,
                'phone' => 9876543210,
                'date_of_birth' => Carbon::today()->subYear('25')->subDays(rand(0, 365)),
                'gender' => 'female',
                'application_source_id' => $source->random(),
                'recruit_job_id' => $job_id->random(),
                'recruit_application_status_id' => $status->random(),
                'location_id' => 1,
                'total_experience' => '1-2',
                'current_location' => 'India',
                'current_ctc' => '350',
                'expected_ctc' => '400',
                'currenct_ctc_rate' => 'Year',
                'expected_ctc_rate' => 'Year',
                'notice_period' => '15',
                'application_sources' => 'addedByUser',
                'added_by' => $user->random()
            ],
            [
                'full_name' => $faker->name,
                'email' => $faker->email,
                'phone' => 9876543210,
                'date_of_birth' => Carbon::today()->subYear('24')->subDays(rand(0, 365)),
                'gender' => 'male',
                'application_source_id' => $source->random(),
                'recruit_job_id' => $job_id->random(),
                'recruit_application_status_id' => $status->random(),
                'location_id' => 1,
                'total_experience' => '1-2',
                'current_location' => 'India',
                'current_ctc' => '360',
                'expected_ctc' => '450',
                'currenct_ctc_rate' => 'Year',
                'expected_ctc_rate' => 'Year',
                'notice_period' => '15',
                'application_sources' => 'addedByUser',
                'added_by' => $user->random()
            ],
            [
                'full_name' => $faker->name,
                'email' => $faker->email,
                'phone' => 9876543210,
                'date_of_birth' => Carbon::today()->subYear('24')->subDays(rand(0, 365)),
                'gender' => 'male',
                'application_source_id' => $source->random(),
                'recruit_job_id' => $job_id->random(),
                'recruit_application_status_id' => $status->random(),
                'location_id' => 1,
                'total_experience' => '1-2',
                'current_location' => 'India',
                'current_ctc' => '360',
                'expected_ctc' => '450',
                'currenct_ctc_rate' => 'Year',
                'expected_ctc_rate' => 'Year',
                'notice_period' => '15',
                'application_sources' => 'addedByUser',
                'added_by' => $user->random(),
                'deleted_at' => Carbon::today()->subDays('24'),
            ],
            [
                'full_name' => $faker->name,
                'email' => $faker->email,
                'phone' => 9876543210,
                'date_of_birth' => Carbon::today()->subYear('24')->subDays(rand(0, 365)),
                'gender' => 'male',
                'application_source_id' => $source->random(),
                'recruit_job_id' => $job_id->random(),
                'recruit_application_status_id' => $status->random(),
                'location_id' => 1,
                'total_experience' => '1-2',
                'current_location' => 'India',
                'current_ctc' => '360',
                'expected_ctc' => '450',
                'currenct_ctc_rate' => 'Year',
                'expected_ctc_rate' => 'Year',
                'notice_period' => '15',
                'application_sources' => 'addedByUser',
                'added_by' => $user->random(),
                'deleted_at' => Carbon::today()->subDays('24'),
            ],
            [
                'full_name' => $faker->name,
                'email' => $faker->email,
                'phone' => 9876543210,
                'date_of_birth' => Carbon::today()->subYear('24')->subDays(rand(0, 365)),
                'gender' => 'male',
                'application_source_id' => $source->random(),
                'recruit_job_id' => $job_id->random(),
                'recruit_application_status_id' => $status->random(),
                'location_id' => 1,
                'total_experience' => '1-2',
                'current_location' => 'India',
                'current_ctc' => '360',
                'expected_ctc' => '450',
                'currenct_ctc_rate' => 'Year',
                'expected_ctc_rate' => 'Year',
                'notice_period' => '15',
                'application_sources' => 'addedByUser',
                'added_by' => $user->random(),
                'deleted_at' => Carbon::today()->subDays('24'),
            ],
            [
                'full_name' => $faker->name,
                'email' => $faker->email,
                'phone' => 9876543210,
                'date_of_birth' => Carbon::today()->subYear('24')->subDays(rand(0, 365)),
                'gender' => 'male',
                'application_source_id' => $source->random(),
                'recruit_job_id' => $job_id->random(),
                'recruit_application_status_id' => $status->random(),
                'location_id' => 1,
                'total_experience' => '1-2',
                'current_location' => 'India',
                'current_ctc' => '360',
                'expected_ctc' => '450',
                'currenct_ctc_rate' => 'Year',
                'expected_ctc_rate' => 'Year',
                'notice_period' => '15',
                'application_sources' => 'addedByUser',
                'added_by' => $user->random(),
                'deleted_at' => Carbon::today()->subDays('24'),
            ]
        ];

        foreach ($jobApps as $jobApp) {
            $jobApp['company_id'] = $companyId;
            RecruitJobApplication::create($jobApp);
        }
    }

}
