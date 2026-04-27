<?php

namespace Modules\Recruit\Database\Seeders;

use App\Models\CompanyAddress;
use App\Models\Currency;
use App\Models\EmployeeDetails;
use App\Models\Team;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Modules\Recruit\Entities\JobInterviewStage;
use Modules\Recruit\Entities\Recruiter;
use Modules\Recruit\Entities\RecruitInterviewStage;
use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Entities\RecruitJobAddress;
use Modules\Recruit\Entities\RecruitJobCategory;
use Modules\Recruit\Entities\RecruitJobSkill;
use Modules\Recruit\Entities\RecruitJobSubCategory;
use Modules\Recruit\Entities\RecruitJobType;
use Modules\Recruit\Entities\RecruitSkill;
use Modules\Recruit\Entities\RecruitWorkExperience;

class JobsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param int $companyId
     * @return void
     */
    public function run($companyId)
    {
        try {
            $faker = Factory::create();

            // Get required data with error handling
            $user = User::where('company_id', $companyId)->pluck('id');
            $emp = EmployeeDetails::where('company_id', $companyId)->pluck('user_id');
            $skills = RecruitSkill::where('company_id', $companyId)->pluck('id');
            $addressID = CompanyAddress::where('company_id', $companyId)->pluck('id');
            $department = Team::where('company_id', $companyId)->pluck('id');
            $experience = RecruitWorkExperience::where('company_id', $companyId)->pluck('id');
            $jobType = RecruitJobType::where('company_id', $companyId)->pluck('id');
            $stages = RecruitInterviewStage::where('company_id', $companyId)->pluck('id');
            $currencyID = Currency::where('company_id', $companyId)->pluck('id');

            // Validate required data
            if ($emp->isEmpty()) {
                throw new \Exception("No employees found for company ID: {$companyId}");
            }

            if ($skills->isEmpty()) {
                throw new \Exception("No skills found for company ID: {$companyId}");
            }

            if ($addressID->isEmpty()) {
                throw new \Exception("No company addresses found for company ID: {$companyId}");
            }

            if ($department->isEmpty()) {
                throw new \Exception("No departments found for company ID: {$companyId}");
            }

            if ($experience->isEmpty()) {
                throw new \Exception("No work experience records found for company ID: {$companyId}");
            }

            if ($jobType->isEmpty()) {
                throw new \Exception("No job types found for company ID: {$companyId}");
            }

            if ($stages->isEmpty()) {
                throw new \Exception("No interview stages found for company ID: {$companyId}");
            }

            if ($currencyID->isEmpty()) {
                throw new \Exception("No currencies found for company ID: {$companyId}");
            }

            if ($user->isEmpty()) {
                throw new \Exception("No users found for company ID: {$companyId}");
            }

            // Create recruiter
            try {
                $recruiter = new Recruiter;
                $recruiter->company_id = $companyId;
                $recruiter->user_id = $emp->random();
                $recruiter->added_by = $emp->random();
                $recruiter->save();
            } catch (\Exception $e) {
                Log::error("Failed to create recruiter: " . $e->getMessage());
                throw new \Exception("Failed to create recruiter: " . $e->getMessage());
            }

            // Create job category
            try {
                $jobCategory = new RecruitJobCategory;
                $jobCategory->company_id = $companyId;
                $jobCategory->category_name = 'Developer';
                $jobCategory->save();
            } catch (\Exception $e) {
                Log::error("Failed to create job category: " . $e->getMessage());
                throw new \Exception("Failed to create job category: " . $e->getMessage());
            }

            // Create job sub-category
            try {
                $jobSubCategory = new RecruitJobSubCategory;
                $jobSubCategory->company_id = $companyId;
                $jobSubCategory->recruit_job_category_id = $jobCategory->id;
                $jobSubCategory->sub_category_name = 'Laravel Developer';
                $jobSubCategory->save();
            } catch (\Exception $e) {
                Log::error("Failed to create job sub-category: " . $e->getMessage());
                throw new \Exception("Failed to create job sub-category: " . $e->getMessage());
            }

            // Get recruiter IDs (fixed variable naming conflict)
            $recruiterIds = Recruiter::with('user')
                ->where('company_id', $companyId)
                ->pluck('user_id');

            if ($recruiterIds->isEmpty()) {
                throw new \Exception("No recruiters found for company ID: {$companyId}");
            }

            $jobs = [
                [
                    'title' => 'Software Developer',
                    'slug' => 'software-developer',
                    'total_positions' => 12,
                    'department_id' => $department->random(),
                    'recruiter_id' => $recruiterIds->random(),
                    'job_type' => 'part time',
                    'recruit_work_experience_id' => $experience->random(),
                    'recruit_job_category_id' => $jobCategory->id,
                    'recruit_job_sub_category_id' => $jobSubCategory->id,
                    'currency_id' => $currencyID->random(),
                    'pay_type' => 'Range',
                    'start_amount' => 645,
                    'end_amount' => 4500,
                    'pay_according' => 'day',
                    'start_date' => now()->format('Y-m-d H:i:s'),
                    'end_date' => now()->addDays('20')->format('Y-m-d H:i:s'),
                    'status' => 'open',
                    'disclose_salary' => 'yes',
                    'is_photo_require' => 0,
                    'is_resume_require' => 0,
                    'is_dob_require' => 0,
                    'is_gender_require' => 0,
                    'recruit_job_type_id' => $jobType->random(),
                    'job_description' => $faker->text(),
                    'remaining_openings' => 12,
                    'added_by' => $user->random(),
                ],
                [
                    'title' => 'Software Tester',
                    'slug' => 'software-tester',
                    'total_positions' => 41,
                    'department_id' => $department->random(),
                    'recruiter_id' => $recruiterIds->random(),
                    'job_type' => 'part time',
                    'recruit_work_experience_id' => $experience->random(),
                    'recruit_job_category_id' => $jobCategory->id,
                    'recruit_job_sub_category_id' => $jobSubCategory->id,
                    'currency_id' => $currencyID->random(),
                    'pay_type' => 'Range',
                    'start_amount' => 245,
                    'end_amount' => 452,
                    'pay_according' => 'day',
                    'start_date' => now()->format('Y-m-d H:i:s'),
                    'end_date' => now()->addDays('10')->format('Y-m-d H:i:s'),
                    'status' => 'open',
                    'is_photo_require' => 0,
                    'is_resume_require' => 0,
                    'is_dob_require' => 0,
                    'is_gender_require' => 0,
                    'recruit_job_type_id' => $jobType->random(),
                    'job_description' => $faker->text(),
                    'remaining_openings' => 41,
                    'added_by' => $user->random(),
                ],
                [
                    'title' => 'Designer',
                    'slug' => 'designer',
                    'total_positions' => 52,
                    'department_id' => $department->random(),
                    'recruiter_id' => $recruiterIds->random(),
                    'job_type' => 'part time',
                    'recruit_work_experience_id' => $experience->random(),
                    'recruit_job_category_id' => $jobCategory->id,
                    'recruit_job_sub_category_id' => $jobSubCategory->id,
                    'currency_id' => $currencyID->random(),
                    'pay_type' => 'Range',
                    'start_amount' => 95,
                    'end_amount' => 150,
                    'pay_according' => 'day',
                    'start_date' => now()->format('Y-m-d H:i:s'),
                    'end_date' => now()->addDays('10')->format('Y-m-d H:i:s'),
                    'status' => 'open',
                    'is_photo_require' => 0,
                    'is_resume_require' => 0,
                    'is_dob_require' => 0,
                    'is_gender_require' => 0,
                    'recruit_job_type_id' => $jobType->random(),
                    'job_description' => $faker->text(),
                    'remaining_openings' => 52,
                    'added_by' => $user->random(),
                ],
                [
                    'title' => 'UI/UX developer',
                    'slug' => 'ui-developer',
                    'total_positions' => 32,
                    'department_id' => $department->random(),
                    'recruiter_id' => $recruiterIds->random(),
                    'job_type' => 'part time',
                    'recruit_work_experience_id' => $experience->random(),
                    'recruit_job_category_id' => $jobCategory->id,
                    'recruit_job_sub_category_id' => $jobSubCategory->id,
                    'currency_id' => $currencyID->random(),
                    'pay_type' => 'Range',
                    'start_amount' => 155,
                    'end_amount' => 250,
                    'pay_according' => 'day',
                    'start_date' => now()->format('Y-m-d H:i:s'),
                    'end_date' => null,
                    'status' => 'open',
                    'disclose_salary' => 'yes',
                    'is_photo_require' => 0,
                    'is_resume_require' => 0,
                    'is_dob_require' => 0,
                    'is_gender_require' => 0,
                    'recruit_job_type_id' => $jobType->random(),
                    'job_description' => $faker->text(),
                    'remaining_openings' => 32,
                    'added_by' => $user->random(),
                ],
            ];

            foreach ($jobs as $index => $job) {
                try {
                    $job['company_id'] = $companyId;
                    $data = RecruitJob::create($job);

                    // Create job skill
                    try {
                        $skill = new RecruitJobSkill;
                        $skill->recruit_job_id = $data->id;
                        $skill->recruit_skill_id = $skills->random();
                        $skill->save();
                    } catch (\Exception $e) {
                        Log::error("Failed to create job skill for job {$data->id}: " . $e->getMessage());
                        // Continue with other operations
                    }

                    // Create job address
                    try {
                        $address = new RecruitJobAddress;
                        $address->recruit_job_id = $data->id;
                        $address->company_address_id = $addressID->random();
                        $address->save();
                    } catch (\Exception $e) {
                        Log::error("Failed to create job address for job {$data->id}: " . $e->getMessage());
                        // Continue with other operations
                    }

                    // Create job interview stage
                    try {
                        $stage = new JobInterviewStage;
                        $stage->recruit_job_id = $data->id;
                        $stage->recruit_interview_stage_id = $stages->random();
                        $stage->save();
                    } catch (\Exception $e) {
                        Log::error("Failed to create job interview stage for job {$data->id}: " . $e->getMessage());
                        // Continue with other operations
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to create job at index {$index}: " . $e->getMessage());
                    // Continue with next job
                    continue;
                }
            }
        } catch (\Exception $e) {
            Log::error("JobsTableSeeder failed for company ID {$companyId}: " . $e->getMessage());
            throw $e;
        }
    }
}
