<?php

namespace Modules\SupplyChain\Policies;

use App\Models\User;
use Modules\SupplyChain\Entities\PurchaseOrder;

class PurchaseOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('supplychain.purchasing.view');
    }

    public function view(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->can('supplychain.purchasing.view') && ((int) $purchaseOrder->company_id === (int) $user->company_id);
    }

    public function create(User $user): bool
    {
        return $user->can('supplychain.purchasing.manage');
    }

    public function update(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->can('supplychain.purchasing.manage') && ((int) $purchaseOrder->company_id === (int) $user->company_id);
    }
}
