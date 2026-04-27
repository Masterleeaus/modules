<?php

namespace Modules\PMCore\app\Enums;

use Carbon\Carbon;

enum RecurrenceType: string
{
    case NONE         = 'none';
    case WEEKLY       = 'weekly';
    case FORTNIGHTLY  = 'fortnightly';
    case MONTHLY      = 'monthly';
    case QUARTERLY    = 'quarterly';

    public function label(): string
    {
        return match ($this) {
            self::NONE        => __('One-off (No Recurrence)'),
            self::WEEKLY      => __('Weekly'),
            self::FORTNIGHTLY => __('Fortnightly'),
            self::MONTHLY     => __('Monthly'),
            self::QUARTERLY   => __('Quarterly'),
        };
    }

    /**
     * Calculate the next occurrence date from a given base date.
     */
    public function nextDate(Carbon $from): Carbon
    {
        return match ($this) {
            self::NONE        => $from->copy(),
            self::WEEKLY      => $from->copy()->addWeek(),
            self::FORTNIGHTLY => $from->copy()->addWeeks(2),
            self::MONTHLY     => $from->copy()->addMonth(),
            self::QUARTERLY   => $from->copy()->addMonths(3),
        };
    }
}
