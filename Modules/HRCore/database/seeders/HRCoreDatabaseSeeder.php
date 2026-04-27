<?php

namespace Modules\HRCore\Database\Seeders;

use Illuminate\Database\Seeder;

class HRCoreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            HRCorePermissionSeeder::class,
            LeaveTypeSeeder::class,
            ExpenseTypeSeeder::class,
            HRCoreSettingsSeeder::class,
            HRCoreDemoDataSeeder::class,  // This includes teams, shifts, departments, holidays
            AttendanceSeeder::class,       // Demo attendance data
            LeaveRequestSeeder::class,     // Demo leave requests
            ExpenseRequestSeeder::class,   // Demo expense requests
        ]);
    }
}
