<?php

namespace Modules\Biometric\Events;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired after a biometric clock-in or clock-out is recorded in the core
 * `attendances` table.  Listeners can use this event to send notifications,
 * link booking IDs, or trigger FSM/field-service workflows.
 */
class BiometricClockIn
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Attendance $attendance,
        public readonly User $user,
        /** One of: fingerprint | face | nfc | gps | pin | manual */
        public readonly string $method,
        public readonly bool $geofencePassed,
        public readonly ?float $lat,
        public readonly ?float $lng,
        public readonly ?int $bookingId,
        public readonly ?string $deviceId,
    ) {
    }
}
