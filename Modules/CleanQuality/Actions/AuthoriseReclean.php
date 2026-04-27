<?php

namespace Modules\CleanQuality\Actions;

use Modules\CleanQuality\Entities\Inspection;
use Modules\CleanQuality\Events\RecleanAuthorised;
use Modules\CleanQuality\Support\Enums\InspectionStatus;

class AuthoriseReclean
{
    public function execute(Inspection $inspection, ?int $recleanBookingId = null): Inspection
    {
        $inspection->status = InspectionStatus::RECLEAN_BOOKED;
        $inspection->reclean_booking_id = $recleanBookingId;
        $inspection->approved_at = now();
        $inspection->save();

        event(new RecleanAuthorised($inspection));

        return $inspection;
    }
}
