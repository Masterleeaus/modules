<?php

namespace Modules\WorksuiteWorkOrders\Listeners;

use Modules\WorksuiteWorkOrders\Events\WorkOrderCompleted;
use Modules\WorksuiteWorkOrders\Entities\WorkOrdersSetting;

class AutoConvertOnCompletion
{
    public function handle(WorkOrderCompleted $event): void
    {
        $settings = WorkOrdersSetting::getOrCreate();
        if ($settings->auto_convert_on_complete) {
            $event->workOrder->loadMissing('tasks');
            $event->workOrder->convertToProject();
        }
    }
}
