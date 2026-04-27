<?php

namespace Modules\WorksuiteWorkOrders\Policies;

use App\Models\User;
use Modules\WorksuiteWorkOrders\Entities\WorkOrder;

class WorkOrderPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, WorkOrder $workOrder): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, WorkOrder $workOrder): bool { return true; }
    public function delete(User $user, WorkOrder $workOrder): bool { return true; }
}
