<?php

namespace Modules\Recruit\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Modules\Recruit\Entities\RecruitWorkExperience;

class WorkExperienceTableSeeder extends Seeder
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
            $workExperiences = [
                'Fresher',
                '1-2 years',
                '2-3 years',
                '3-5 years',
                '5-8 years',
                '8-12 years',
                '12+ years'
            ];

            foreach ($workExperiences as $experience) {
                try {
                    RecruitWorkExperience::create([
                        'work_experience' => $experience,
                        'company_id' => $companyId
                    ]);
                } catch (\Exception $e) {
                    // Log error but continue with other records
                    Log::error("Failed to create work experience '{$experience}' for company ID {$companyId}: " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            Log::error("WorkExperienceTableSeeder failed for company ID {$companyId}: " . $e->getMessage());
            throw $e;
        }
    }
}
