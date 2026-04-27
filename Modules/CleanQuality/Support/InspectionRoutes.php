<?php

namespace Modules\CleanQuality\Support;

use Modules\CleanQuality\Support\Contracts\RouteNamer;

final class InspectionRoutes implements RouteNamer
{
    public const RECURRING_INDEX = 'recurring-inspection_schedules.index';
    public const SCHEDULES_INDEX = 'inspection_schedules.index';
    public const INSPECTIONS_INDEX = 'schedule-inspection.index';

    public static function all(): array
    {
        return [
            self::RECURRING_INDEX,
            self::SCHEDULES_INDEX,
            self::INSPECTIONS_INDEX,
        ];
    }
}
