<?php

namespace Modules\HRCore\database\seeders;

use App\Enums\LeaveRequestStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\HRCore\app\Models\LeaveRequest;
use Modules\HRCore\app\Models\LeaveType;

class LeaveRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users except clients
        $users = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'client');
        })->get();

        if ($users->isEmpty()) {
            $this->command->warn('No users found for creating leave requests');

            return;
        }

        // Get all leave types
        $leaveTypes = LeaveType::all();

        if ($leaveTypes->isEmpty()) {
            $this->command->warn('No leave types found. Please run LeaveTypeSeeder first.');

            return;
        }

        // Create leave requests for the past 6 months and future 3 months
        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now()->addMonths(3);

        foreach ($users as $user) {
            // Create 3-8 leave requests per user
            $requestCount = rand(3, 8);

            for ($i = 0; $i < $requestCount; $i++) {
                $this->createLeaveRequest($user, $leaveTypes, $startDate, $endDate);
            }
        }

        $this->command->info('Leave requests seeded successfully!');
    }

    /**
     * Create a single leave request
     */
    private function createLeaveRequest(User $user, $leaveTypes, Carbon $startDate, Carbon $endDate)
    {
        $leaveType = $leaveTypes->random();

        // Generate random dates
        $fromDate = Carbon::create(
            rand($startDate->year, $endDate->year),
            rand($startDate->month, $endDate->month),
            rand(1, 28)
        );

        // Randomly decide if half-day leave (20% chance)
        $isHalfDay = rand(1, 100) <= 20;

        if ($isHalfDay) {
            $toDate = $fromDate->copy(); // Same day for half-day
            $totalDays = 0.5;
            $halfDayType = rand(0, 1) ? 'first_half' : 'second_half';
        } else {
            // Leave duration: 1-10 days
            $duration = rand(1, 10);
            $toDate = $fromDate->copy()->addDays($duration - 1);
            $totalDays = $this->calculateWorkingDays($fromDate, $toDate);
            $halfDayType = null;
        }

        // Determine status based on date
        $status = $this->determineStatus($fromDate);

        // Randomly add emergency contact (30% chance)
        $hasEmergencyContact = rand(1, 100) <= 30;

        // Randomly add travel abroad info (10% chance)
        $isAbroad = rand(1, 100) <= 10;

        // Create the leave request
        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'from_date' => $fromDate->format('Y-m-d'),
            'to_date' => $toDate->format('Y-m-d'),
            'is_half_day' => $isHalfDay,
            'half_day_type' => $halfDayType,
            'total_days' => $totalDays,
            'user_notes' => $this->generateUserNotes($leaveType->name),
            'status' => $status->value,
            'emergency_contact' => $hasEmergencyContact ? $this->generateEmergencyContact() : null,
            'emergency_phone' => $hasEmergencyContact ? $this->generatePhoneNumber() : null,
            'is_abroad' => $isAbroad,
            'abroad_location' => $isAbroad ? $this->generateAbroadLocation() : null,
            'created_by_id' => $user->id,
            'updated_by_id' => $user->id,
        ]);

        // Add approval data if status is not pending
        if ($status !== LeaveRequestStatus::PENDING) {
            $this->addApprovalData($leaveRequest, $status);
        }
    }

    /**
     * Determine leave request status based on date
     */
    private function determineStatus(Carbon $fromDate): LeaveRequestStatus
    {
        $now = Carbon::now();

        if ($fromDate->isFuture()) {
            // Future leaves: 70% approved, 20% pending, 10% rejected
            $rand = rand(1, 100);
            if ($rand <= 70) {
                return LeaveRequestStatus::APPROVED;
            }
            if ($rand <= 90) {
                return LeaveRequestStatus::PENDING;
            }

            return LeaveRequestStatus::REJECTED;
        } else {
            // Past leaves: 80% approved, 15% rejected, 5% cancelled
            $rand = rand(1, 100);
            if ($rand <= 80) {
                return LeaveRequestStatus::APPROVED;
            }
            if ($rand <= 95) {
                return LeaveRequestStatus::REJECTED;
            }

            return LeaveRequestStatus::CANCELLED;
        }
    }

    /**
     * Generate user notes for the leave request
     */
    private function generateUserNotes(string $leaveTypeName): string
    {
        $notes = [
            'Annual Leave' => [
                'Planning to spend time with family',
                'Going on vacation to the beach',
                'Need some rest and relaxation',
                'Traveling to visit relatives',
                'Taking a break to recharge',
            ],
            'Sick Leave' => [
                'Feeling unwell and need to rest',
                'Doctor advised to take rest',
                'Recovering from flu symptoms',
                'Not feeling well enough to work',
                'Need medical attention',
            ],
            'Emergency Leave' => [
                'Family emergency requires immediate attention',
                'Urgent personal matter to attend to',
                'Emergency situation at home',
                'Unexpected urgent issue',
            ],
            'Personal Leave' => [
                'Personal matter to attend to',
                'Need to handle personal affairs',
                'Taking care of personal business',
                'Personal commitment',
            ],
            'Study Leave' => [
                'Attending professional development course',
                'Exam preparation time needed',
                'Educational seminar to attend',
                'Completing certification requirements',
            ],
            'Work From Home' => [
                'Need to work from home today',
                'Home office setup for better productivity',
                'Avoiding commute due to weather',
                'Working remotely for personal reasons',
            ],
        ];

        $typeNotes = $notes[$leaveTypeName] ?? ['Taking leave for '.strtolower($leaveTypeName)];

        return $typeNotes[array_rand($typeNotes)];
    }

    /**
     * Add approval data to the leave request
     */
    private function addApprovalData(LeaveRequest $leaveRequest, LeaveRequestStatus $status)
    {
        // Get a random user as approver (could be HR or manager)
        $approver = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['super_admin', 'hr_manager', 'manager']);
        })->inRandomOrder()->first();

        if (! $approver) {
            $approver = User::whereDoesntHave('roles', function ($q) {
                $q->where('name', 'client');
            })->inRandomOrder()->first();
        }

        switch ($status) {
            case LeaveRequestStatus::APPROVED:
                $leaveRequest->update([
                    'approved_by_id' => $approver->id,
                    'approved_at' => Carbon::now()->subDays(rand(1, 30)),
                    'approval_notes' => $this->generateApprovalNotes(true),
                ]);
                break;

            case LeaveRequestStatus::REJECTED:
                $leaveRequest->update([
                    'rejected_by_id' => $approver->id,
                    'rejected_at' => Carbon::now()->subDays(rand(1, 30)),
                    'approval_notes' => $this->generateApprovalNotes(false),
                ]);
                break;

            case LeaveRequestStatus::CANCELLED:
                $leaveRequest->update([
                    'cancelled_at' => Carbon::now()->subDays(rand(1, 15)),
                    'cancel_reason' => $this->generateCancelReason(),
                ]);
                break;
        }
    }

    /**
     * Generate approval notes
     */
    private function generateApprovalNotes(bool $approved): string
    {
        if ($approved) {
            $notes = [
                'Leave approved. Please ensure proper handover.',
                'Approved. Enjoy your time off.',
                'Request approved. Make sure to complete pending tasks.',
                'Approved as requested.',
                'Leave granted. Have a good rest.',
            ];
        } else {
            $notes = [
                'Cannot approve due to project deadlines.',
                'Insufficient leave balance.',
                'Business critical period, please reschedule.',
                'Team capacity issues during requested period.',
                'Please reapply for different dates.',
            ];
        }

        return $notes[array_rand($notes)];
    }

    /**
     * Generate cancel reason
     */
    private function generateCancelReason(): string
    {
        $reasons = [
            'Personal circumstances changed',
            'Work commitments arose',
            'Family plans were postponed',
            'Health issue resolved',
            'Travel plans cancelled',
            'Financial constraints',
            'Emergency situation resolved',
        ];

        return $reasons[array_rand($reasons)];
    }

    /**
     * Calculate working days between two dates
     */
    private function calculateWorkingDays(Carbon $fromDate, Carbon $toDate): float
    {
        $workingDays = 0;
        $currentDate = $fromDate->copy();

        while ($currentDate <= $toDate) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            if ($currentDate->dayOfWeek !== 0 && $currentDate->dayOfWeek !== 6) {
                $workingDays++;
            }
            $currentDate->addDay();
        }

        return $workingDays;
    }

    /**
     * Generate emergency contact name
     */
    private function generateEmergencyContact(): string
    {
        $firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'Robert', 'Lisa'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Davis', 'Miller', 'Wilson'];
        $relations = ['(Spouse)', '(Parent)', '(Sibling)', '(Friend)', '(Relative)'];

        return $firstNames[array_rand($firstNames)].' '.
               $lastNames[array_rand($lastNames)].' '.
               $relations[array_rand($relations)];
    }

    /**
     * Generate phone number
     */
    private function generatePhoneNumber(): string
    {
        return '+1-'.rand(200, 999).'-'.rand(200, 999).'-'.rand(1000, 9999);
    }

    /**
     * Generate abroad location
     */
    private function generateAbroadLocation(): string
    {
        $locations = [
            'United States - New York',
            'United Kingdom - London',
            'France - Paris',
            'Germany - Berlin',
            'Italy - Rome',
            'Spain - Madrid',
            'Japan - Tokyo',
            'Australia - Sydney',
            'Canada - Toronto',
            'Singapore',
            'Dubai, UAE',
            'Thailand - Bangkok',
        ];

        return $locations[array_rand($locations)];
    }
}
