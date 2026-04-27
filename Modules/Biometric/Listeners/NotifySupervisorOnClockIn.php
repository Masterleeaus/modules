<?php

namespace Modules\Biometric\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Biometric\Events\BiometricClockIn;
use Modules\Biometric\Notifications\BiometricClockInNotification;

/**
 * Notifies the employee's direct supervisor / HR manager when a biometric
 * clock-in event is received.
 */
class NotifySupervisorOnClockIn
{
    public function handle(BiometricClockIn $event): void
    {
        try {
            $user = $event->user;

            // Resolve the supervisor via EmployeeDetails if available
            $supervisorId = null;

            if (class_exists(\App\Models\EmployeeDetails::class)) {
                $details = \App\Models\EmployeeDetails::where('user_id', $user->id)->first();
                $supervisorId = $details?->reporting_to ?? null;
            }

            if (! $supervisorId) {
                return;
            }

            $supervisor = \App\Models\User::find($supervisorId);

            if (! $supervisor) {
                return;
            }

            $supervisor->notify(
                new BiometricClockInNotification($event, 'supervisor')
            );
        } catch (\Throwable $e) {
            Log::warning('[Biometric] NotifySupervisorOnClockIn failed: ' . $e->getMessage());
        }
    }
}
