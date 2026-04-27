<?php

namespace Modules\HRCore\database\seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\HRCore\app\Models\Department;
use Modules\HRCore\app\Models\Designation;
use Modules\HRCore\app\Models\Holiday;
use Modules\HRCore\app\Models\LeaveBalanceAdjustment;
use Modules\HRCore\app\Models\LeaveType;
use Modules\HRCore\app\Models\Shift;
use Modules\HRCore\app\Models\Team;
use Modules\HRCore\app\Models\UserAvailableLeave;

class HRCoreDemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds for demo data
     */
    public function run(): void
    {
        $this->command->info('Seeding HRCore demo data...');

        $this->seedTeams();
        $this->seedShifts();
        $this->seedDepartmentsAndDesignations();
        $this->seedHolidays();
        $this->seedLeaveBalances();

        $this->command->info('HRCore demo data seeded successfully!');
    }

    private function seedTeams()
    {
        $teams = [
            ['name' => 'Default Team', 'code' => 'TM-001', 'status' => 'active'],
            ['name' => 'Sales Team 1', 'code' => 'TM-002', 'status' => 'active'],
            ['name' => 'Demo Team', 'code' => 'TM-003', 'status' => 'active'],
            ['name' => 'Team 3', 'code' => 'TM-004', 'status' => 'active'],
        ];

        foreach ($teams as $team) {
            Team::firstOrCreate(
                ['code' => $team['code']],
                $team
            );
        }
    }

    private function seedShifts()
    {
        $shifts = [
            [
                'name' => 'Default Shift',
                'code' => 'SH-001',
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
            ],
            [
                'name' => 'Evening Shift',
                'code' => 'SH-002',
                'status' => 'active',
                'start_time' => '14:00:00',
                'end_time' => '22:00:00',
                'sunday' => false,
                'monday' => true,
                'tuesday' => true,
                'wednesday' => true,
                'thursday' => true,
                'friday' => true,
                'saturday' => false,
            ],
            [
                'name' => 'Night Shift',
                'code' => 'SH-003',
                'status' => 'active',
                'start_time' => '22:00:00',
                'end_time' => '06:00:00',
                'sunday' => false,
                'monday' => true,
                'tuesday' => true,
                'wednesday' => true,
                'thursday' => true,
                'friday' => true,
                'saturday' => false,
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::firstOrCreate(
                ['code' => $shift['code']],
                $shift
            );
        }
    }

    private function seedDepartmentsAndDesignations(): void
    {
        $departments = [
            [
                'name' => 'Default Department',
                'code' => 'DEPT-001',
                'notes' => 'Default Department',
                'designations' => [
                    ['name' => 'Default Designation', 'code' => 'DES-001', 'notes' => 'Default Designation'],
                ],
            ],
            [
                'name' => 'Sales Department',
                'code' => 'DEPT-002',
                'notes' => 'Sales Department',
                'designations' => [
                    ['name' => 'Sales Manager', 'code' => 'DES-002', 'notes' => 'Sales Manager'],
                    ['name' => 'Sales Executive', 'code' => 'DES-003', 'notes' => 'Sales Executive'],
                    ['name' => 'Sales Associate', 'code' => 'DES-004', 'notes' => 'Sales Associate'],
                    ['name' => 'Sales Representative', 'code' => 'DES-005', 'notes' => 'Sales Representative'],
                ],
            ],
            [
                'name' => 'HR Department',
                'code' => 'DEPT-003',
                'notes' => 'HR Department',
                'designations' => [
                    [
                        'name' => 'HR Manager',
                        'code' => 'DES-006',
                        'notes' => 'HR Manager',
                        'is_leave_approver' => true,
                        'is_expense_approver' => true,
                        'is_loan_approver' => true,
                        'is_document_approver' => true,
                    ],
                    ['name' => 'HR Executive', 'code' => 'DES-007', 'notes' => 'HR Executive'],
                    ['name' => 'HR Associate', 'code' => 'DES-008', 'notes' => 'HR Associate'],
                ],
            ],
            [
                'name' => 'IT Department',
                'code' => 'DEPT-004',
                'notes' => 'IT Department',
                'designations' => [
                    ['name' => 'IT Manager', 'code' => 'DES-009', 'notes' => 'IT Manager'],
                    ['name' => 'IT Executive', 'code' => 'DES-010', 'notes' => 'IT Executive'],
                    ['name' => 'IT Associate', 'code' => 'DES-011', 'notes' => 'IT Associate'],
                ],
            ],
            [
                'name' => 'Finance Department',
                'code' => 'DEPT-005',
                'notes' => 'Finance Department',
                'designations' => [
                    ['name' => 'Finance Manager', 'code' => 'DES-012', 'notes' => 'Finance Manager'],
                    ['name' => 'Finance Executive', 'code' => 'DES-013', 'notes' => 'Finance Executive'],
                    ['name' => 'Finance Associate', 'code' => 'DES-014', 'notes' => 'Finance Associate'],
                ],
            ],
            [
                'name' => 'Marketing Department',
                'code' => 'DEPT-006',
                'notes' => 'Marketing Department',
                'designations' => [
                    ['name' => 'Marketing Manager', 'code' => 'DES-015', 'notes' => 'Marketing Manager'],
                    ['name' => 'Marketing Executive', 'code' => 'DES-016', 'notes' => 'Marketing Executive'],
                    ['name' => 'Marketing Associate', 'code' => 'DES-017', 'notes' => 'Marketing Associate'],
                ],
            ],
            [
                'name' => 'Operations Department',
                'code' => 'DEPT-007',
                'notes' => 'Operations Department',
                'designations' => [
                    ['name' => 'Operations Manager', 'code' => 'DES-018', 'notes' => 'Operations Manager'],
                    ['name' => 'Operations Executive', 'code' => 'DES-019', 'notes' => 'Operations Executive'],
                    ['name' => 'Operations Associate', 'code' => 'DES-020', 'notes' => 'Operations Associate'],
                ],
            ],
            [
                'name' => 'Admin Department',
                'code' => 'DEPT-008',
                'notes' => 'Admin Department',
                'designations' => [
                    ['name' => 'Admin Manager', 'code' => 'DES-021', 'notes' => 'Admin Manager'],
                    ['name' => 'Admin Executive', 'code' => 'DES-022', 'notes' => 'Admin Executive'],
                    ['name' => 'Admin Associate', 'code' => 'DES-023', 'notes' => 'Admin Associate'],
                ],
            ],
        ];

        foreach ($departments as $deptData) {
            $department = Department::firstOrCreate(
                ['code' => $deptData['code']],
                [
                    'name' => $deptData['name'],
                    'notes' => $deptData['notes'],
                ]
            );

            foreach ($deptData['designations'] as $desigData) {
                Designation::firstOrCreate(
                    ['code' => $desigData['code']],
                    array_merge($desigData, ['department_id' => $department->id])
                );
            }
        }
    }

    private function seedHolidays()
    {
        $holidays = [
            ['name' => 'New Year', 'date' => Carbon::parse('2025-01-01'), 'code' => 'NY', 'notes' => 'New Year Holiday'],
            ['name' => 'Pongal', 'date' => Carbon::parse('2025-01-14'), 'code' => 'PONGAL', 'notes' => 'Pongal Holiday'],
            ['name' => 'Republic Day', 'date' => Carbon::parse('2025-01-26'), 'code' => 'RD', 'notes' => 'Republic Day Holiday'],
            ['name' => 'Good Friday', 'date' => Carbon::parse('2025-04-18'), 'code' => 'GF', 'notes' => 'Good Friday Holiday'],
            ['name' => 'May Day', 'date' => Carbon::parse('2025-05-01'), 'code' => 'MD', 'notes' => 'May Day Holiday'],
            ['name' => 'Independence Day', 'date' => Carbon::parse('2025-08-15'), 'code' => 'ID', 'notes' => 'Independence Day Holiday'],
        ];

        foreach ($holidays as $holiday) {
            Holiday::firstOrCreate(
                ['code' => $holiday['code']],
                $holiday
            );
        }
    }

    private function seedLeaveBalances(): void
    {
        $this->command->info('Seeding leave balances for demo users...');

        // Get all users except clients
        $users = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'client');
        })->get();

        // Get all leave types
        $leaveTypes = LeaveType::all();

        if ($leaveTypes->isEmpty()) {
            $this->command->warn('No leave types found. Skipping leave balance seeding.');

            return;
        }

        $currentYear = date('Y');

        foreach ($users as $user) {
            foreach ($leaveTypes as $leaveType) {
                $entitledLeaves = 0;
                $usedLeaves = 0;
                $additionalLeaves = 0;
                $carriedForward = 0;

                // Skip gender-specific leaves for inappropriate users
                if ($leaveType->code === 'ML' && $user->gender !== 'female') {
                    continue;
                }
                if ($leaveType->code === 'PTL' && $user->gender !== 'male') {
                    continue;
                }

                // Calculate service years for the user
                $serviceYears = $user->date_of_joining ?
                    Carbon::parse($user->date_of_joining)->diffInYears(now()) : 0;

                // Skip sabbatical for users with less than 5 years of service
                if ($leaveType->code === 'SAB' && $serviceYears < 5) {
                    continue;
                }

                // Set leave balances based on leave type
                switch ($leaveType->code) {
                    case 'CL': // Casual Leave
                        $entitledLeaves = 12;
                        $usedLeaves = rand(0, 8);
                        $carriedForward = rand(0, 3);
                        break;

                    case 'SL': // Sick Leave
                        $entitledLeaves = 6;
                        $usedLeaves = rand(0, 4);
                        break;

                    case 'EL': // Earned Leave
                        $entitledLeaves = 21;
                        $usedLeaves = rand(0, 10);
                        $carriedForward = rand(0, 15);
                        break;

                    case 'ML': // Maternity Leave
                        $entitledLeaves = 182;
                        $usedLeaves = rand(0, 1) ? 182 : 0;
                        break;

                    case 'PTL': // Paternity Leave
                        $entitledLeaves = 15;
                        $usedLeaves = rand(0, 1) ? 15 : 0;
                        break;

                    case 'CO': // Compensatory Off
                        $entitledLeaves = 0;
                        $additionalLeaves = rand(0, 10);
                        $usedLeaves = rand(0, min(5, $additionalLeaves));
                        $carriedForward = rand(0, min(3, $additionalLeaves - $usedLeaves));
                        break;

                    case 'WFH': // Work From Home
                        $entitledLeaves = 52;
                        $usedLeaves = rand(10, 30);
                        break;

                    default:
                        if ($leaveType->is_accrual_enabled) {
                            $entitledLeaves = $leaveType->max_accrual_limit ?? 10;
                            $usedLeaves = rand(0, min(5, $entitledLeaves));
                        }
                        break;
                }

                $availableLeaves = $entitledLeaves + $additionalLeaves + $carriedForward - $usedLeaves;

                // Skip if no balance to create
                if ($entitledLeaves == 0 && $additionalLeaves == 0 && $carriedForward == 0) {
                    continue;
                }

                // Create leave balance record
                UserAvailableLeave::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'leave_type_id' => $leaveType->id,
                        'year' => $currentYear,
                    ],
                    [
                        'entitled_leaves' => $entitledLeaves,
                        'carried_forward_leaves' => $carriedForward,
                        'additional_leaves' => $additionalLeaves,
                        'used_leaves' => $usedLeaves,
                        'available_leaves' => $availableLeaves,
                        'created_by_id' => 1,
                        'updated_by_id' => 1,
                    ]
                );

                // Create adjustment record for initial balance (only if table exists)
                if (Schema::hasTable('leave_balance_adjustments')) {
                    LeaveBalanceAdjustment::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'leave_type_id' => $leaveType->id,
                            'year' => $currentYear,
                            'adjustment_type' => 'add',
                            'reason' => 'Initial balance setup for demo data',
                        ],
                        [
                            'days' => $entitledLeaves,
                            'effective_date' => now(),
                            'balance_before' => 0,
                            'balance_after' => $entitledLeaves,
                            'created_by_id' => 1,
                            'updated_by_id' => 1,
                        ]
                    );
                }
            }
        }

        $this->command->info('Leave balances seeded successfully!');
    }
}
