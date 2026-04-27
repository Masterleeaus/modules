<?php

namespace Modules\HRCore\database\seeders;

use App\Enums\Status;
use Illuminate\Database\Seeder;
use Modules\HRCore\app\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding comprehensive leave types...');

        $leaves = [
            // Regular Leaves
            [
                'name' => 'Casual Leave',
                'code' => 'CL',
                'notes' => 'Short-term personal leave for urgent work',
                'is_proof_required' => false,
                'is_accrual_enabled' => true,
                'accrual_frequency' => 'monthly',
                'accrual_days' => 1,
                'max_accrual_days' => 12,
                'is_carry_forward_enabled' => true,
                'max_carry_forward_days' => 3,
                'carry_forward_expire_days' => 90,
                'is_encashable' => false,
                'max_consecutive_days' => 3,
                'advance_notice_days' => 1,
                'is_half_day_allowed' => true,
            ],
            [
                'name' => 'Sick Leave',
                'code' => 'SL',
                'notes' => 'Medical leave for health issues',
                'is_proof_required' => true,
                'proof_required_after_days' => 2,
                'is_accrual_enabled' => true,
                'accrual_frequency' => 'monthly',
                'accrual_days' => 0.5,
                'max_accrual_days' => 6,
                'is_carry_forward_enabled' => false,
                'is_encashable' => false,
                'max_consecutive_days' => 15,
                'advance_notice_days' => 0,
                'is_half_day_allowed' => true,
            ],
            [
                'name' => 'Earned Leave',
                'code' => 'EL',
                'notes' => 'Annual vacation leave earned through service',
                'is_proof_required' => false,
                'is_accrual_enabled' => true,
                'accrual_frequency' => 'monthly',
                'accrual_days' => 1.75,
                'max_accrual_days' => 21,
                'is_carry_forward_enabled' => true,
                'max_carry_forward_days' => 15,
                'carry_forward_expire_days' => 365,
                'is_encashable' => true,
                'max_encashment_days' => 15,
                'encashment_rate' => 100,
                'max_consecutive_days' => 20,
                'advance_notice_days' => 7,
                'is_half_day_allowed' => true,
            ],

            // Parental Leaves
            [
                'name' => 'Maternity Leave',
                'code' => 'ML',
                'notes' => 'Leave for expecting and new mothers',
                'is_proof_required' => true,
                'is_accrual_enabled' => false,
                'is_carry_forward_enabled' => false,
                'is_encashable' => false,
                'max_consecutive_days' => 182, // 26 weeks
                'advance_notice_days' => 30,
                'is_half_day_allowed' => false,
                'fixed_entitlement' => 182,
            ],
            [
                'name' => 'Paternity Leave',
                'code' => 'PTL',
                'notes' => 'Leave for new fathers',
                'is_proof_required' => true,
                'is_accrual_enabled' => false,
                'is_carry_forward_enabled' => false,
                'is_encashable' => false,
                'max_consecutive_days' => 15,
                'advance_notice_days' => 7,
                'is_half_day_allowed' => false,
                'fixed_entitlement' => 15,
            ],
            [
                'name' => 'Adoption Leave',
                'code' => 'AL',
                'notes' => 'Leave for adoptive parents',
                'is_proof_required' => true,
                'is_accrual_enabled' => false,
                'is_carry_forward_enabled' => false,
                'is_encashable' => false,
                'max_consecutive_days' => 84, // 12 weeks
                'advance_notice_days' => 14,
                'is_half_day_allowed' => false,
                'fixed_entitlement' => 84,
            ],

            // Special Purpose Leaves
            [
                'name' => 'Bereavement Leave',
                'code' => 'BL',
                'notes' => 'Leave for death in family',
                'is_proof_required' => false,
                'is_accrual_enabled' => false,
                'is_carry_forward_enabled' => false,
                'is_encashable' => false,
                'max_consecutive_days' => 5,
                'advance_notice_days' => 0,
                'is_half_day_allowed' => false,
                'fixed_entitlement' => 5,
            ],
            [
                'name' => 'Marriage Leave',
                'code' => 'MRL',
                'notes' => 'Leave for own marriage or immediate family marriage',
                'is_proof_required' => true,
                'is_accrual_enabled' => false,
                'is_carry_forward_enabled' => false,
                'is_encashable' => false,
                'max_consecutive_days' => 7,
                'advance_notice_days' => 30,
                'is_half_day_allowed' => false,
                'fixed_entitlement' => 7,
            ],
            [
                'name' => 'Study Leave',
                'code' => 'STL',
                'notes' => 'Leave for exams and educational purposes',
                'is_proof_required' => true,
                'is_accrual_enabled' => false,
                'is_carry_forward_enabled' => false,
                'is_encashable' => false,
                'max_consecutive_days' => 10,
                'advance_notice_days' => 14,
                'is_half_day_allowed' => true,
                'fixed_entitlement' => 10,
            ],

            // Compensatory and Flexible Leaves
            [
                'name' => 'Compensatory Off',
                'code' => 'CO',
                'notes' => 'Compensation for working on holidays or weekends',
                'is_proof_required' => false,
                'is_accrual_enabled' => false,
                'is_carry_forward_enabled' => true,
                'max_carry_forward_days' => 30,
                'carry_forward_expire_days' => 90,
                'is_encashable' => false,
                'max_consecutive_days' => 2,
                'advance_notice_days' => 2,
                'is_half_day_allowed' => true,
                'is_comp_off_leave' => true,
            ],
            [
                'name' => 'Work From Home',
                'code' => 'WFH',
                'notes' => 'Remote work arrangement',
                'is_proof_required' => false,
                'is_accrual_enabled' => false,
                'is_carry_forward_enabled' => false,
                'is_encashable' => false,
                'max_consecutive_days' => 5,
                'advance_notice_days' => 1,
                'is_half_day_allowed' => true,
                'affects_attendance' => false,
            ],

            // Unpaid and Extended Leaves
            [
                'name' => 'Leave Without Pay',
                'code' => 'LWP',
                'notes' => 'Unpaid leave for personal reasons',
                'is_proof_required' => true,
                'is_accrual_enabled' => false,
                'is_carry_forward_enabled' => false,
                'is_encashable' => false,
                'max_consecutive_days' => 90,
                'advance_notice_days' => 7,
                'is_half_day_allowed' => true,
                'is_paid' => false,
            ],
            [
                'name' => 'Sabbatical Leave',
                'code' => 'SAB',
                'notes' => 'Extended career break for personal development',
                'is_proof_required' => true,
                'is_accrual_enabled' => false,
                'is_carry_forward_enabled' => false,
                'is_encashable' => false,
                'max_consecutive_days' => 365,
                'advance_notice_days' => 60,
                'is_half_day_allowed' => false,
                'is_paid' => false,
                'min_service_years' => 5,
            ],

            // Medical and Emergency Leaves
            [
                'name' => 'Medical Leave',
                'code' => 'MDL',
                'notes' => 'Extended leave for medical treatment or surgery',
                'is_proof_required' => true,
                'is_accrual_enabled' => false,
                'is_carry_forward_enabled' => false,
                'is_encashable' => false,
                'max_consecutive_days' => 90,
                'advance_notice_days' => 0,
                'is_half_day_allowed' => false,
                'fixed_entitlement' => 15,
            ],
            [
                'name' => 'Emergency Leave',
                'code' => 'EML',
                'notes' => 'Urgent unforeseen personal emergency',
                'is_proof_required' => false,
                'is_accrual_enabled' => false,
                'is_carry_forward_enabled' => false,
                'is_encashable' => false,
                'max_consecutive_days' => 3,
                'advance_notice_days' => 0,
                'is_half_day_allowed' => true,
                'fixed_entitlement' => 3,
            ],
            [
                'name' => 'Quarantine Leave',
                'code' => 'QL',
                'notes' => 'Mandatory isolation for infectious diseases',
                'is_proof_required' => true,
                'is_accrual_enabled' => false,
                'is_carry_forward_enabled' => false,
                'is_encashable' => false,
                'max_consecutive_days' => 14,
                'advance_notice_days' => 0,
                'is_half_day_allowed' => false,
            ],

            // Legal and Civic Leaves
            [
                'name' => 'Jury Duty Leave',
                'code' => 'JDL',
                'notes' => 'Leave for jury service or court appearance',
                'is_proof_required' => true,
                'is_accrual_enabled' => false,
                'is_carry_forward_enabled' => false,
                'is_encashable' => false,
                'max_consecutive_days' => 30,
                'advance_notice_days' => 7,
                'is_half_day_allowed' => true,
            ],
            [
                'name' => 'Voting Leave',
                'code' => 'VL',
                'notes' => 'Leave to exercise voting rights',
                'is_proof_required' => false,
                'is_accrual_enabled' => false,
                'is_carry_forward_enabled' => false,
                'is_encashable' => false,
                'max_consecutive_days' => 1,
                'advance_notice_days' => 2,
                'is_half_day_allowed' => true,
                'fixed_entitlement' => 1,
            ],

            // Other Special Leaves
            [
                'name' => 'Birthday Leave',
                'code' => 'BDL',
                'notes' => 'Special leave on employee birthday',
                'is_proof_required' => false,
                'is_accrual_enabled' => false,
                'is_carry_forward_enabled' => false,
                'is_encashable' => false,
                'max_consecutive_days' => 1,
                'advance_notice_days' => 0,
                'is_half_day_allowed' => false,
                'fixed_entitlement' => 1,
            ],
            [
                'name' => 'Relocation Leave',
                'code' => 'RL',
                'notes' => 'Leave for relocating residence',
                'is_proof_required' => false,
                'is_accrual_enabled' => false,
                'is_carry_forward_enabled' => false,
                'is_encashable' => false,
                'max_consecutive_days' => 3,
                'advance_notice_days' => 7,
                'is_half_day_allowed' => false,
                'fixed_entitlement' => 3,
            ],
        ];

        foreach ($leaves as $leave) {
            LeaveType::updateOrCreate(
                ['code' => $leave['code']], // Find by code
                [
                    'name' => $leave['name'],
                    'notes' => $leave['notes'],
                    'is_proof_required' => $leave['is_proof_required'] ?? false,

                    // Accrual settings - map to actual field names
                    'is_accrual_enabled' => $leave['is_accrual_enabled'] ?? false,
                    // Set accrual frequency to 'monthly' as default if accrual is enabled
                    'accrual_frequency' => $leave['is_accrual_enabled'] ?
                      ($leave['accrual_frequency'] ?? 'monthly') : 'monthly',
                    'accrual_rate' => $leave['accrual_days'] ?? 0,
                    'max_accrual_limit' => $leave['max_accrual_days'] ?? null,

                    // Carry forward settings - map to actual field names
                    'allow_carry_forward' => $leave['is_carry_forward_enabled'] ?? false,
                    'max_carry_forward' => $leave['max_carry_forward_days'] ?? null,
                    'carry_forward_expiry_months' => isset($leave['carry_forward_expire_days']) ?
                      round($leave['carry_forward_expire_days'] / 30) : null,

                    // Encashment settings - map to actual field names
                    'allow_encashment' => $leave['is_encashable'] ?? false,
                    'max_encashment_days' => $leave['max_encashment_days'] ?? null,

                    // Comp off type
                    'is_comp_off_type' => $leave['is_comp_off_leave'] ?? false,

                    'status' => Status::ACTIVE,
                ]
            );
        }

        $this->command->info('Comprehensive leave types seeded successfully!');
    }
}
