<?php

namespace Modules\CleanQuality\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\CleanQuality\Entities\Schedule;

class QualityCheckScheduled
{
    use Dispatchable, SerializesModels;

    public function __construct(public Schedule $schedule)
    {
    }
}

