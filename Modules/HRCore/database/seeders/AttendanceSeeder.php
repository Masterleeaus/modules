<?php

namespace Modules\HRCore\Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\HRCore\app\Models\Attendance;
use Modules\HRCore\app\Models\AttendanceLog;
use Modules\HRCore\app\Models\Shift;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all active users
        $users = User::where('status', 'active')->take(10)->get();

        if ($users->isEmpty()) {
            $this->command->info('No active users found. Please seed users first.');

            return;
        }

        // Get default shift (if exists)
        $shift = Shift::first();

        // Generate attendance for the last 7 days
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(Carbon::now()->subDays($i));
        }

        foreach ($dates as $date) {
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }

            foreach ($users as $user) {
                // Random chance of being absent (10%)
                if (rand(1, 100) <= 10) {
                    continue;
                }

                // Create attendance record
                $attendance = Attendance::firstOrCreate([
                    'user_id' => $user->id,
                    'date' => $date->toDateString(),
                ], [
                    'shift_id' => $shift?->id,
                    'status' => $this->getRandomStatus(),
                    'created_at' => $date->copy()->setTime(8, 0, 0),
                    'updated_at' => $date->copy()->setTime(8, 0, 0),
                ]);

                // Random check-in time (8:00 AM - 9:30 AM)
                $checkInHour = 8;
                $checkInMinute = rand(0, 90); // 0-90 minutes after 8 AM
                $checkInTime = $date->copy()->setTime($checkInHour, 0, 0)->addMinutes($checkInMinute);

                // Create check-in log
                AttendanceLog::create([
                    'attendance_id' => $attendance->id,
                    'user_id' => $user->id,
                    'date' => $date->toDateString(),
                    'time' => $checkInTime->format('H:i:s'),
                    'logged_at' => $checkInTime,
                    'type' => 'check_in',
                    'shift_id' => $shift?->id,
                    'latitude' => $this->getRandomLatitude(),
                    'longitude' => $this->getRandomLongitude(),
                    'address' => $this->getRandomAddress(),
                    'created_at' => $checkInTime,
                    'updated_at' => $checkInTime,
                ]);

                // Random chance of checking out (90%)
                if (rand(1, 100) <= 90) {
                    // Random check-out time (4:00 PM - 6:30 PM)
                    $checkOutHour = 16; // 4 PM
                    $checkOutMinute = rand(0, 150); // 0-150 minutes after 4 PM
                    $checkOutTime = $date->copy()->setTime($checkOutHour, 0, 0)->addMinutes($checkOutMinute);

                    // Create check-out log
                    AttendanceLog::create([
                        'attendance_id' => $attendance->id,
                        'user_id' => $user->id,
                        'date' => $date->toDateString(),
                        'time' => $checkOutTime->format('H:i:s'),
                        'logged_at' => $checkOutTime,
                        'type' => 'check_out',
                        'shift_id' => $shift?->id,
                        'latitude' => $this->getRandomLatitude(),
                        'longitude' => $this->getRandomLongitude(),
                        'address' => $this->getRandomAddress(),
                        'created_at' => $checkOutTime,
                        'updated_at' => $checkOutTime,
                    ]);

                    // Update attendance with check-out time
                    $attendance->update([
                        'check_out_time' => $checkOutTime,
                    ]);
                }

                // Update attendance with check-in time
                $attendance->update([
                    'check_in_time' => $checkInTime,
                ]);
            }
        }

        // Add some attendance records for today with various states
        $today = Carbon::today();
        $todayUsers = $users->shuffle()->take(7);

        foreach ($todayUsers as $index => $user) {
            $attendance = Attendance::firstOrCreate([
                'user_id' => $user->id,
                'date' => $today->toDateString(),
            ], [
                'shift_id' => $shift?->id,
                'status' => 'present',
                'created_at' => $today,
                'updated_at' => $today,
            ]);

            if ($index < 5) {
                // First 5 users have checked in
                $checkInTime = $today->copy()->setTime(8, rand(0, 45), 0);

                AttendanceLog::create([
                    'attendance_id' => $attendance->id,
                    'user_id' => $user->id,
                    'date' => $today->toDateString(),
                    'time' => $checkInTime->format('H:i:s'),
                    'logged_at' => $checkInTime,
                    'type' => 'check_in',
                    'shift_id' => $shift?->id,
                    'latitude' => $this->getRandomLatitude(),
                    'longitude' => $this->getRandomLongitude(),
                    'address' => $this->getRandomAddress(),
                    'created_at' => $checkInTime,
                    'updated_at' => $checkInTime,
                ]);

                $attendance->update([
                    'check_in_time' => $checkInTime,
                    'status' => $checkInTime->copy()->setTime(9, 0, 0)->lt($checkInTime) ? 'late' : 'present',
                ]);

                if ($index < 2) {
                    // First 2 users have also checked out
                    $checkOutTime = $today->copy()->setTime(17, rand(0, 30), 0);

                    AttendanceLog::create([
                        'attendance_id' => $attendance->id,
                        'user_id' => $user->id,
                        'date' => $today->toDateString(),
                        'time' => $checkOutTime->format('H:i:s'),
                        'logged_at' => $checkOutTime,
                        'type' => 'check_out',
                        'shift_id' => $shift?->id,
                        'latitude' => $this->getRandomLatitude(),
                        'longitude' => $this->getRandomLongitude(),
                        'address' => $this->getRandomAddress(),
                        'created_at' => $checkOutTime,
                        'updated_at' => $checkOutTime,
                    ]);

                    $attendance->update([
                        'check_out_time' => $checkOutTime,
                    ]);
                }
            }
        }

        $this->command->info('Attendance records seeded successfully!');
    }

    /**
     * Get random status
     */
    private function getRandomStatus(): string
    {
        $statuses = ['present', 'late', 'absent'];

        return $statuses[array_rand($statuses)];
    }

    /**
     * Get random latitude (example: around New York area)
     */
    private function getRandomLatitude(): float
    {
        return 40.7128 + (rand(-100, 100) / 10000);
    }

    /**
     * Get random longitude (example: around New York area)
     */
    private function getRandomLongitude(): float
    {
        return -74.0060 + (rand(-100, 100) / 10000);
    }

    /**
     * Get random address
     */
    private function getRandomAddress(): string
    {
        $streets = [
            '123 Main Street, New York, NY 10001',
            '456 Broadway, New York, NY 10013',
            '789 5th Avenue, New York, NY 10022',
            '321 Park Avenue, New York, NY 10016',
            '654 Madison Avenue, New York, NY 10065',
            '987 Wall Street, New York, NY 10005',
            '147 Times Square, New York, NY 10036',
            '258 Central Park West, New York, NY 10024',
        ];

        return $streets[array_rand($streets)];
    }
}
