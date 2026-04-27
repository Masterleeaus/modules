<?php

namespace Modules\Biometric\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Biometric\Events\BiometricClockIn;
use Modules\Biometric\Notifications\BiometricGeofenceFailedNotification;

/**
 * Fires when the geofence check fails.  Notifies the company admin /
 * scheduler so they can follow up with the cleaner.
 */
class AlertOnGeofenceFailure
{
    public function handle(BiometricClockIn $event): void
    {
        if ($event->geofencePassed) {
            return; // Nothing to do
        }

        try {
            $user = $event->user;

            // Notify all admins of the company
            $admins = \App\Models\User::allAdmins($user->company_id);

            foreach ($admins as $admin) {
                $admin->notify(new BiometricGeofenceFailedNotification($event));
            }
        } catch (\Throwable $e) {
            Log::warning('[Biometric] AlertOnGeofenceFailure failed: ' . $e->getMessage());
        }
    }
}
