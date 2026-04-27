<?php

namespace Modules\CleanQuality\Actions;

use Modules\CleanQuality\Entities\Inspection;
use Modules\CleanQuality\Events\InspectionCompleted;
use Modules\CleanQuality\Support\Enums\InspectionStatus;

class CompleteInspection
{
    public function execute(Inspection $inspection, array $attributes = []): Inspection
    {
        $inspection->fill($attributes);
        $inspection->status = $attributes['status'] ?? InspectionStatus::PASSED;
        $inspection->inspected_at = $attributes['inspected_at'] ?? now();
        $inspection->save();

        event(new InspectionCompleted($inspection));

        return $inspection;
    }
}
