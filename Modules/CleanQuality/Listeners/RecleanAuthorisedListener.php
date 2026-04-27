<?php

namespace Modules\CleanQuality\Listeners;

use Modules\CleanQuality\Events\RecleanAuthorised;

class RecleanAuthorisedListener
{
    private const SIGNAL_BOOKING = 'booking_module.reclean.authorised';

    public function handle(RecleanAuthorised $event): void
    {
        event(self::SIGNAL_BOOKING, [
            'company_id' => $event->inspection->company_id ?? null,
            'inspection_id' => $event->inspection->id ?? null,
            'booking_id' => $event->inspection->booking_id ?? null,
            'inspector_id' => $event->inspection->inspector_id ?? null,
        ]);
    }
}

