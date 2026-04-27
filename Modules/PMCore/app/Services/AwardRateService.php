<?php

namespace Modules\PMCore\app\Services;

use Carbon\Carbon;
use Modules\PMCore\app\Models\Timesheet;

class AwardRateService
{
    /**
     * AU public holidays (national + VIC defaults).
     * Keyed as 'YYYY-MM-DD'.
     */
    private const PUBLIC_HOLIDAYS = [
        'VIC' => [
            // 2025
            '2025-01-01', // New Year's Day
            '2025-01-27', // Australia Day (observed)
            '2025-04-18', // Good Friday
            '2025-04-19', // Easter Saturday
            '2025-04-20', // Easter Sunday
            '2025-04-21', // Easter Monday
            '2025-04-25', // ANZAC Day
            '2025-06-09', // King's Birthday (VIC)
            '2025-11-04', // Melbourne Cup Day
            '2025-12-25', // Christmas Day
            '2025-12-26', // Boxing Day
            // 2026
            '2026-01-01', // New Year's Day
            '2026-01-26', // Australia Day
            '2026-04-03', // Good Friday
            '2026-04-04', // Easter Saturday
            '2026-04-05', // Easter Sunday
            '2026-04-06', // Easter Monday
            '2026-04-25', // ANZAC Day
            '2026-06-08', // King's Birthday (VIC)
            '2026-11-03', // Melbourne Cup Day
            '2026-12-25', // Christmas Day
            '2026-12-28', // Boxing Day (observed)
        ],
        'NSW' => [
            '2025-01-01', '2025-01-27', '2025-04-18', '2025-04-19',
            '2025-04-21', '2025-04-25', '2025-06-09', '2025-08-04',
            '2025-10-06', '2025-12-25', '2025-12-26',
        ],
        'QLD' => [
            '2025-01-01', '2025-01-27', '2025-04-18', '2025-04-19',
            '2025-04-21', '2025-04-25', '2025-05-05', '2025-08-13',
            '2025-10-06', '2025-12-25', '2025-12-26',
        ],
        'WA' => [
            '2025-01-01', '2025-01-27', '2025-03-03', '2025-04-18',
            '2025-04-21', '2025-04-25', '2025-06-02', '2025-09-22',
            '2025-12-25', '2025-12-26',
        ],
    ];

    /**
     * Award multipliers for the cleaning industry.
     */
    private const MULTIPLIERS = [
        'standard'       => 1.00,
        'casual'         => 1.25,
        'weekend'        => 1.50,
        'public_holiday' => 2.25,
        'overtime'       => 1.50,
    ];

    /**
     * Apply the award rate multiplier to a base hourly rate.
     */
    public function calculateRate(string $rateType, float $baseRate): float
    {
        $multiplier = self::MULTIPLIERS[$rateType] ?? 1.00;

        return round($baseRate * $multiplier, 4);
    }

    /**
     * Determine the award rate type that applies on a given date/state.
     */
    public function getHolidayRate(Carbon $date, string $state = 'VIC'): string
    {
        $dateStr = $date->format('Y-m-d');
        $holidays = self::PUBLIC_HOLIDAYS[$state] ?? self::PUBLIC_HOLIDAYS['VIC'];

        if (in_array($dateStr, $holidays, true)) {
            return 'public_holiday';
        }

        // Saturday = 6, Sunday = 0
        $dayOfWeek = (int) $date->format('N'); // 1=Mon … 7=Sun
        if ($dayOfWeek >= 6) {
            return 'weekend';
        }

        return 'standard';
    }

    /**
     * Calculate the total labour cost for a timesheet entry.
     * Uses clock-in/out duration when available, falling back to hours.
     */
    public function calculateTimesheetCost(Timesheet $timesheet, float $baseRate): float
    {
        $rateType = $timesheet->award_rate_type ?? 'standard';
        $multiplier = (float) ($timesheet->award_rate_multiplier ?? self::MULTIPLIERS[$rateType] ?? 1.00);
        $effectiveRate = $baseRate * $multiplier;

        // Derive hours from clock-in/out if available
        if ($timesheet->clock_in_at && $timesheet->clock_out_at) {
            $minutes = $timesheet->clock_in_at->diffInMinutes($timesheet->clock_out_at);
            $hours = $minutes / 60;
        } else {
            $hours = (float) $timesheet->hours;
        }

        return round($hours * $effectiveRate, 2);
    }
}
