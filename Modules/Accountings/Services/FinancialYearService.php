<?php

namespace Modules\Accountings\Services;

use Carbon\Carbon;

/**
 * Calculates the financial year string from a date.
 *
 * Australian financial year runs July 1 – June 30.
 * Standard (calendar) financial year runs January 1 – December 31.
 *
 * Returns strings like "FY2025" where the year is the end-year of the FY.
 */
class FinancialYearService
{
    /**
     * Derive the financial year label from a given date.
     *
     * @param  string|Carbon|\DateTimeInterface  $date
     * @param  string  $type  'au' for Australian (Jul–Jun), 'standard' for Jan–Dec
     * @return string  e.g. "FY2025"
     */
    public function fromDate(mixed $date, string $type = 'au'): string
    {
        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);

        $year = match ($type) {
            'au'       => $this->australianFinancialYear($carbon),
            default    => $carbon->year,
        };

        return 'FY' . $year;
    }

    /**
     * Return the ending year of the Australian financial year for a date.
     * AU FY ends 30 June, so dates in Jan–Jun belong to the FY ending that year,
     * while dates in Jul–Dec belong to the FY ending the following year.
     */
    private function australianFinancialYear(Carbon $date): int
    {
        // Jul (7) – Dec (12) → FY ends next calendar year
        if ($date->month >= 7) {
            return $date->year + 1;
        }
        // Jan (1) – Jun (6) → FY ends this calendar year
        return $date->year;
    }
}
