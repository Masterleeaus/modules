<?php

namespace Modules\HRCore\database\seeders;

use App\Models\ModuleSetting;
use Illuminate\Database\Seeder;

class HRCoreSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $settings = [
            // Employee Management
            'employee_code_prefix' => 'EMP',
            'employee_code_start_number' => '1000',
            'default_probation_period' => '90',
            'default_password' => '123456',

            // Attendance Management
            'is_auto_check_out_enabled' => false,
            'auto_check_out_time' => '18:00',
            'is_multiple_check_in_enabled' => true,

            // Leave Management
            'require_leave_approval' => true,
            'min_advance_notice_days' => '1',
            'weekend_included_in_leave' => false,
            'holidays_included_in_leave' => false,

            // Shift Management
            'default_shift_duration' => '8',
        ];

        foreach ($settings as $key => $value) {
            ModuleSetting::firstOrCreate(
                [
                    'module' => 'HRCore',
                    'key' => $key,
                ],
                [
                    'value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
