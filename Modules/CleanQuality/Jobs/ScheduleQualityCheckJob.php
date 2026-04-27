<?php

namespace Modules\CleanQuality\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\CleanQuality\Entities\Schedule;
use Modules\CleanQuality\Events\QualityCheckScheduled;

class ScheduleQualityCheckJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $scheduleId)
    {
    }

    public function handle(): void
    {
        $schedule = Schedule::query()->find($this->scheduleId);

        if (! $schedule) {
            return;
        }

        event(new QualityCheckScheduled($schedule));
    }
}

