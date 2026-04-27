<?php

namespace Modules\Biometric\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Biometric\Events\BiometricClockIn;
use Modules\Biometric\Notifications\BiometricClockInNotification;

/**
 * When a biometric clock-in is linked to a BookingModule booking, notifies
 * the client that their cleaner has arrived.  Guards against BookingModule
 * not being installed.
 */
class NotifyClientOnCleanerArrival
{
    public function handle(BiometricClockIn $event): void
    {
        if (! $event->bookingId) {
            return;
        }

        if (! class_exists(\Modules\BookingModule\Entities\Booking::class)) {
            return;
        }

        try {
            $booking = \Modules\BookingModule\Entities\Booking::find($event->bookingId);

            if (! $booking) {
                return;
            }

            // Resolve the client user from the booking
            $clientUserId = $booking->user_id ?? $booking->customer_id ?? null;

            if (! $clientUserId) {
                return;
            }

            $client = \App\Models\User::find($clientUserId);

            if (! $client) {
                return;
            }

            $client->notify(
                new BiometricClockInNotification($event, 'client')
            );
        } catch (\Throwable $e) {
            Log::warning('[Biometric] NotifyClientOnCleanerArrival failed: ' . $e->getMessage());
        }
    }
}
