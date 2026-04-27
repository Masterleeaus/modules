<?php

namespace Modules\Accountings\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Accountings\Entities\AccountingSetting;

class PeriodLockService
{
    public static function lockedDate(): ?Carbon
    {
        $user = Auth::user();
        if (!$user) return null;

        $setting = AccountingSetting::where('company_id', $user->company_id)
            ->where('user_id', $user->id)
            ->first();

        return $setting?->period_lock_date ? Carbon::parse($setting->period_lock_date) : null;
    }

    public static function assertOpen($date): void
    {
        if (!$date) return;
        $lock = self::lockedDate();
        if (!$lock) return;

        $d = $date instanceof Carbon ? $date : Carbon::parse($date);
        // locked on or before the lock date
        if ($d->lessThanOrEqualTo($lock)) {
            abort(403, 'Accounting period is locked for dates on or before '.$lock->toDateString());
        }
    }
}
