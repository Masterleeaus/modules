<?php

namespace Modules\CleanQuality\Listeners;

use Modules\CleanQuality\Events\QualityScoreUpdated;

class QualityScoreUpdatedListener
{
    private const SIGNAL_PERFORMANCE = 'performance.cleaner_quality_score_updated';
    private const SIGNAL_PREMISES = 'managed_premises.quality_score_updated';
    private const SIGNAL_TITANZERO = 'titanzero.clean_quality.score_updated';

    public function handle(QualityScoreUpdated $event): void
    {
        event(self::SIGNAL_PERFORMANCE, [
            'company_id' => $event->schedule->company_id ?? null,
            'cleaner_id' => $event->schedule->worker_id ?? null,
            'booking_id' => $event->schedule->job_id ?? null,
            'schedule_id' => $event->schedule->id ?? null,
            'score' => $event->schedule->score ?? null,
        ]);

        event(self::SIGNAL_PREMISES, [
            'company_id' => $event->schedule->company_id ?? null,
            'property_id' => $event->schedule->property_id ?? null,
            'schedule_id' => $event->schedule->id ?? null,
            'score' => $event->schedule->score ?? null,
        ]);

        event(self::SIGNAL_TITANZERO, [
            'company_id' => $event->schedule->company_id ?? null,
            'schedule_id' => $event->schedule->id ?? null,
            'score' => $event->schedule->score ?? null,
        ]);
    }
}

