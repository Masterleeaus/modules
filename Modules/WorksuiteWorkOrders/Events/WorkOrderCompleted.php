<?php

namespace Modules\WorksuiteWorkOrders\Events;

use Modules\WorksuiteWorkOrders\Entities\WorkOrder;

class WorkOrderCompleted.php
{
    public function __construct(public WorkOrder $workOrder) {}
    public function topic(): string { return 'workorders.completed'; }
}
