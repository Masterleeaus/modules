<?php

namespace Modules\HRCore\database\seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\HRCore\app\Models\Holiday;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = Carbon::now()->year;

        $holidays = [
            [
                'name' => "New Year's Day",
                'code' => 'NEW_YEAR',
                'date' => Carbon::create($year, 1, 1),
                'type' => 'public',
                'category' => 'national',
                'description' => 'Celebration of the beginning of the new year',
                'is_recurring' => true,
                'applicable_for' => 'all',
                'color' => '#4CAF50',
                'is_active' => true,
                'is_visible_to_employees' => true,
            ],
            [
                'name' => 'Independence Day',
                'code' => 'INDEPENDENCE',
                'date' => Carbon::create($year, 7, 4),
                'type' => 'public',
                'category' => 'national',
                'description' => 'Celebration of national independence',
                'is_recurring' => true,
                'applicable_for' => 'all',
                'color' => '#FF5252',
                'is_active' => true,
                'is_visible_to_employees' => true,
            ],
            [
                'name' => 'Thanksgiving',
                'code' => 'THANKSGIVING',
                'date' => Carbon::create($year, 11, 28),
                'type' => 'public',
                'category' => 'national',
                'description' => 'Day of giving thanks',
                'is_recurring' => true,
                'applicable_for' => 'all',
                'color' => '#FF9800',
                'is_active' => true,
                'is_visible_to_employees' => true,
            ],
            [
                'name' => 'Christmas Day',
                'code' => 'CHRISTMAS',
                'date' => Carbon::create($year, 12, 25),
                'type' => 'religious',
                'category' => 'festival',
                'description' => 'Christmas celebration',
                'is_recurring' => true,
                'applicable_for' => 'all',
                'color' => '#F44336',
                'is_active' => true,
                'is_visible_to_employees' => true,
            ],
            [
                'name' => 'Good Friday',
                'code' => 'GOOD_FRIDAY',
                'date' => Carbon::create($year, 3, 29),
                'type' => 'religious',
                'category' => 'festival',
                'description' => 'Christian religious observance',
                'is_optional' => true,
                'is_recurring' => false,
                'applicable_for' => 'all',
                'color' => '#9C27B0',
                'is_active' => true,
                'is_visible_to_employees' => true,
            ],
            [
                'name' => 'Company Foundation Day',
                'code' => 'FOUNDATION_DAY',
                'date' => Carbon::create($year, 6, 15),
                'type' => 'company',
                'category' => 'company_event',
                'description' => 'Anniversary of company foundation',
                'is_recurring' => true,
                'applicable_for' => 'all',
                'color' => '#2196F3',
                'is_active' => true,
                'is_visible_to_employees' => true,
                'send_notification' => true,
                'notification_days_before' => 14,
            ],
            [
                'name' => 'Summer Friday',
                'code' => 'SUMMER_FRIDAY',
                'date' => Carbon::create($year, 7, 12),
                'type' => 'company',
                'category' => 'company_event',
                'description' => 'Half day off for summer',
                'is_half_day' => true,
                'half_day_type' => 'afternoon',
                'half_day_start_time' => '13:00',
                'half_day_end_time' => '18:00',
                'applicable_for' => 'all',
                'color' => '#00BCD4',
                'is_active' => true,
                'is_visible_to_employees' => true,
            ],
            [
                'name' => 'Regional Holiday - California',
                'code' => 'REGIONAL_CA',
                'date' => Carbon::create($year, 9, 9),
                'type' => 'regional',
                'category' => 'state',
                'description' => 'California Admission Day',
                'applicable_for' => 'location',
                'locations' => ['Los Angeles', 'San Francisco', 'San Diego'],
                'color' => '#FFD700',
                'is_active' => true,
                'is_visible_to_employees' => true,
            ],
            [
                'name' => 'Diwali',
                'code' => 'DIWALI',
                'date' => Carbon::create($year, 11, 1),
                'type' => 'religious',
                'category' => 'festival',
                'description' => 'Festival of lights',
                'is_optional' => true,
                'is_restricted' => true,
                'applicable_for' => 'all',
                'color' => '#FFC107',
                'is_active' => true,
                'is_visible_to_employees' => true,
            ],
            [
                'name' => 'Martin Luther King Jr. Day',
                'code' => 'MLK_DAY',
                'date' => Carbon::create($year, 1, 20),
                'type' => 'public',
                'category' => 'national',
                'description' => 'Civil rights leader commemoration',
                'is_recurring' => true,
                'applicable_for' => 'all',
                'color' => '#795548',
                'is_active' => true,
                'is_visible_to_employees' => true,
            ],
        ];

        foreach ($holidays as $holiday) {
            $date = $holiday['date'];
            Holiday::firstOrCreate(
                ['code' => $holiday['code']],
                array_merge($holiday, [
                    'year' => $date->year,
                    'day' => $date->format('l'),
                ])
            );
        }
    }
}
