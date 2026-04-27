<?php

namespace Modules\CleanQuality\Actions;

use Modules\CleanQuality\Entities\Schedule;
use Modules\CleanQuality\Events\QualityScoreUpdated;

class ScoreQualityCheck
{
    public function execute(Schedule $schedule, float $score): Schedule
    {
        $schedule->score = $score;
        $schedule->save();

        event(new QualityScoreUpdated($schedule));

        return $schedule;
    }
}

