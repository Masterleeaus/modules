<?php

namespace Modules\CleanQuality\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\CleanQuality\Entities\Schedule;

class GenerateQualityReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $scheduleId)
    {
    }

    public function handle(): void
    {
        if (! Schedule::query()->whereKey($this->scheduleId)->exists()) {
            return;
        }

        event('titan_docs.clean_quality.report_requested', [
            'schedule_id' => $this->scheduleId,
        ]);
    }
}
