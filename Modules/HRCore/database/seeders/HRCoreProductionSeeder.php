<?php

namespace Modules\HRCore\database\seeders;

use Illuminate\Database\Seeder;
use Modules\HRCore\app\Models\Department;
use Modules\HRCore\app\Models\Designation;
use Modules\HRCore\app\Models\Shift;
use Modules\HRCore\app\Models\Team;

class HRCoreProductionSeeder extends Seeder
{
    /**
     * Run the database seeds for production with minimal data
     */
    public function run(): void
    {
        $this->command->info('Seeding HRCore production data...');

        // Create default team
        $team = Team::firstOrCreate(
            ['code' => 'TM-001'],
            [
                'name' => 'Default Team',
                'status' => 'active',
            ]
        );

        // Create default shift
        $shift = Shift::firstOrCreate(
            ['code' => 'SH-001'],
            [
                'name' => 'Default Shift',
                'status' => 'active',
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
                'sunday' => false,
                'monday' => true,
                'tuesday' => true,
                'wednesday' => true,
                'thursday' => true,
                'friday' => true,
                'saturday' => false,
            ]
        );

        // Create default department
        $department = Department::firstOrCreate(
            ['code' => 'DEPT-001'],
            [
                'name' => 'Default Department',
                'notes' => 'Default Department',
            ]
        );

        // Create default designation
        $designation = Designation::firstOrCreate(
            ['code' => 'DES-001'],
            [
                'name' => 'Default Designation',
                'department_id' => $department->id,
                'notes' => 'Default Designation',
            ]
        );

        $this->command->info('HRCore production data seeded successfully!');
        $this->command->info('Default Team: '.$team->name);
        $this->command->info('Default Shift: '.$shift->name);
        $this->command->info('Default Department: '.$department->name);
        $this->command->info('Default Designation: '.$designation->name);
    }
}
