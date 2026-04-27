<?php

namespace Modules\Recruit\Database\Seeders;

use App\Models\EmployeeDetails;
use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Recruit\Entities\RecruitInterviewEmployees;
use Modules\Recruit\Entities\RecruitInterviewSchedule;
use Modules\Recruit\Entities\RecruitInterviewStage;
use Modules\Recruit\Entities\RecruitJobApplication;
use Carbon\Carbon;


class InterviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($companyId)
    {
        $users = User::where('company_id', $companyId)->pluck('id');
        $applications = RecruitJobApplication::where('company_id', $companyId)->get();
        $stages = RecruitInterviewStage::where('company_id', $companyId)->pluck('id');

        foreach ($applications as $app) {
            $interviews = [
                [
                    'recruit_job_application_id' => $app->id,
                    'interview_type' => 'in person',
                    'schedule_date' => $this->generateRandomDate(),
                    'status' => 'pending',
                    'recruit_interview_stage_id' => $stages->random(),
                    'user_accept_status' => 'waiting',
                    'video_type' => 'other',
                    'notify_c' => 0,
                    'added_by' => $users->random(),
                ],
            ];

            foreach ($interviews as $interview) {
                $interview['company_id'] = $app->company_id;
                RecruitInterviewSchedule::create($interview);
            }
        }

        $app = RecruitJobApplication::where('company_id', $companyId)->first();
        $inter = new RecruitInterviewSchedule;
        $inter->recruit_job_application_id = $app->id;
        $inter->company_id = $app->company->id;
        $inter->interview_type = 'in person';
        $inter->schedule_date = now();
        $inter->status = 'pending';
        $inter->user_accept_status = 'waiting';
        $inter->recruit_interview_stage_id = 1;
        $inter->video_type = 'other';
        $inter->notify_c = 0;
        $inter->added_by = $users->random();
        $inter->save();

        $user = EmployeeDetails::where('company_id', $companyId)->pluck('user_id');
        $interviewSchedule = RecruitInterviewSchedule::where('company_id', $companyId)->get();

        foreach ($interviewSchedule as $interview) {
            $employees = new RecruitInterviewEmployees;
            $employees->recruit_interview_schedule_id = $interview->id;
            $employees->user_id = $user->random();
            $employees->user_accept_status = 'waiting';
            $employees->save();
        }
    }

    private function generateRandomDate()
    {
        $randomDate = Carbon::now()->addDays(rand(0, 29));
        $randomTime = Carbon::createFromTime(rand(0, 23), rand(0, 59), rand(0, 59));
        return $randomDate->setTime($randomTime->hour, $randomTime->minute, $randomTime->second)->format('Y-m-d H:i:s');
    }
    
}
