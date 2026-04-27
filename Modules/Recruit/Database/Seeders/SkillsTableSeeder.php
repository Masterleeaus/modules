<?php

namespace Modules\Recruit\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Recruit\Entities\RecruitSkill;

class SkillsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($companyId)
    {
        RecruitSkill::insert(
            [
                [
                    'name' => 'CSS',
                    'company_id' => $companyId,
                ],
                [
                    'name' => 'HTML',
                    'company_id' => $companyId,
                ],
                [
                    'name' => 'C++',
                    'company_id' => $companyId,
                ],
                [
                    'name' => 'JavaScript',
                    'company_id' => $companyId,
                ],
                [
                    'name' => 'Java',
                    'company_id' => $companyId,
                ],
            ]
        );
    }
}
