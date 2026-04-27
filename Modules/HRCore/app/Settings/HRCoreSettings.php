<?php

namespace Modules\HRCore\app\Settings;

use App\Services\Settings\BaseModuleSettings;

class HRCoreSettings extends BaseModuleSettings
{
    protected string $module = 'HRCore';

    /**
     * Get module display name
     */
    public function getModuleName(): string
    {
        return __('HR Settings');
    }

    /**
     * Get module description
     */
    public function getModuleDescription(): string
    {
        return __('Configure employee lifecycle, leave policies, and organizational structure settings');
    }

    /**
     * Get module icon
     */
    public function getModuleIcon(): string
    {
        return 'bx bx-group';
    }

    /**
     * Define module settings
     */
    protected function define(): array
    {
        return [
            'employee_management' => [
                'employee_code_prefix' => [
                    'type' => 'text',
                    'label' => __('Employee Code Prefix'),
                    'help' => __('Prefix for auto-generated employee codes'),
                    'default' => 'EMP',
                    'validation' => 'nullable|string|max:10',
                ],
                'employee_code_start_number' => [
                    'type' => 'number',
                    'label' => __('Employee Code Start Number'),
                    'help' => __('Starting number for employee codes'),
                    'default' => '1000',
                    'validation' => 'required|numeric|min:1',
                ],
                'default_probation_period' => [
                    'type' => 'number',
                    'label' => __('Default Probation Period (Days)'),
                    'help' => __('Default probation period for new employees'),
                    'default' => '90',
                    'validation' => 'required|numeric|min:0|max:365',
                ],
                'default_password' => [
                    'type' => 'text',
                    'label' => __('Default Password for New Employees'),
                    'help' => __('Default password assigned to new employees (they should change it on first login)'),
                    'default' => '123456',
                    'validation' => 'required|string|min:6',
                ],
            ],

            'attendance_management' => [
                'is_auto_check_out_enabled' => [
                    'type' => 'toggle',
                    'label' => __('Enable Auto Check-Out'),
                    'help' => __('Automatically check out employees at specified time'),
                    'default' => false,
                    'validation' => 'boolean',
                ],
                'auto_check_out_time' => [
                    'type' => 'time',
                    'label' => __('Auto Check-Out Time'),
                    'help' => __('Default time for automatic check-out'),
                    'default' => '18:00',
                    'validation' => 'required|date_format:H:i',
                ],
                'is_multiple_check_in_enabled' => [
                    'type' => 'toggle',
                    'label' => __('Allow Multiple Check-ins'),
                    'help' => __('Allow employees to check in multiple times per day'),
                    'default' => true,
                    'validation' => 'boolean',
                ],
            ],

            'leave_management' => [
                'require_leave_approval' => [
                    'type' => 'toggle',
                    'label' => __('Require Leave Approval'),
                    'help' => __('All leave requests must be approved by manager'),
                    'default' => true,
                    'validation' => 'boolean',
                ],
                'min_advance_notice_days' => [
                    'type' => 'number',
                    'label' => __('Minimum Advance Notice (Days)'),
                    'help' => __('Minimum days in advance for leave requests'),
                    'default' => '1',
                    'validation' => 'required|numeric|min:0|max:30',
                ],
                'weekend_included_in_leave' => [
                    'type' => 'toggle',
                    'label' => __('Include Weekends in Leave'),
                    'help' => __('Count weekends as part of leave duration'),
                    'default' => false,
                    'validation' => 'boolean',
                ],
                'holidays_included_in_leave' => [
                    'type' => 'toggle',
                    'label' => __('Include Holidays in Leave'),
                    'help' => __('Count holidays as part of leave duration'),
                    'default' => false,
                    'validation' => 'boolean',
                ],
            ],

            'shift_management' => [
                'default_shift_duration' => [
                    'type' => 'number',
                    'label' => __('Default Shift Duration (Hours)'),
                    'help' => __('Default working hours per shift'),
                    'default' => '8',
                    'validation' => 'required|numeric|min:1|max:24',
                ],
            ],
        ];
    }
}
