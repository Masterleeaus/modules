<?php

namespace Modules\CleanQuality\Listeners;

use Modules\CleanQuality\Entities\Inspection;
use Modules\CleanQuality\Events\InspectionCompleted;
use Modules\CleanQuality\Support\Enums\InspectionStatus;

/**
 * Reacts to an Inspection being marked complete.
 *
 * Responsibilities:
 * - Dispatch a signal to downstream Titan modules (Performance, Premises, TitanZero).
 * - If the inspection failed, fire the reclean-required signal so the booking
 *   module can prompt the supervisor to authorise a reclean.
 */
class InspectionCompletedListener
{
    private const SIGNAL_PERFORMANCE = 'performance.inspection_completed';
    private const SIGNAL_PREMISES    = 'managed_premises.inspection_completed';
    private const SIGNAL_TITANZERO   = 'titanzero.clean_quality.inspection_completed';
    private const SIGNAL_RECLEAN     = 'booking_module.reclean.required';

    public function handle(InspectionCompleted $event): void
    {
        $inspection = $event->inspection;

        $base = [
            'company_id'    => $inspection->company_id ?? null,
            'inspection_id' => $inspection->id,
            'booking_id'    => $inspection->booking_id ?? null,
            'inspector_id'  => $inspection->inspector_id ?? null,
            'score'         => $inspection->score ?? null,
            'status'        => $inspection->status,
        ];

        event(self::SIGNAL_PERFORMANCE, $base);
        event(self::SIGNAL_PREMISES, $base);
        event(self::SIGNAL_TITANZERO, $base);

        // If the inspection failed, notify booking module so a reclean can be triggered.
        if ($inspection->status === InspectionStatus::FAILED) {
            event(self::SIGNAL_RECLEAN, $base);
        }
    }
}
