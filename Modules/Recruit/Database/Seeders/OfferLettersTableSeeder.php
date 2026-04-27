<?php

namespace Modules\Recruit\Database\Seeders;

use App\Models\EmployeeDetails;
use Illuminate\Database\Seeder;
use Modules\Recruit\Entities\RecruitJobApplication;
use Modules\Recruit\Entities\RecruitJobOfferLetter;

class OfferLettersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($companyId)
    {
        $user = EmployeeDetails::where('company_id', $companyId)->pluck('user_id');
        $application = RecruitJobApplication::where('company_id', $companyId)->with('job')->get();

        foreach ($application as $app) {
            $letters = [
                [
                    'recruit_job_application_id' => $app->id,
                    'recruit_job_id' => $app->job->id,
                    'job_expire' => now()->addDays('2')->format('Y-m-d H:i:s'),
                    'expected_joining_date' => now()->addDays('5')->format('Y-m-d H:i:s'),
                    'comp_amount' => 132,
                    'status' => 'pending',
                    'pay_according' => 'day',
                    'added_by' => $user->random(),
                    'hash' => md5(microtime()),
                ],
            ];

            foreach ($letters as $letter) {
                $letter['company_id'] = $companyId;
                RecruitJobOfferLetter::create($letter);
            }
        }
    }
}
