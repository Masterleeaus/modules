<?php

namespace Modules\SupplyChain\Policies;

use App\Models\User;
use Modules\SupplyChain\Entities\Item;

class StockItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('supplychain.view');
    }

    public function view(User $user, Item $item): bool
    {
        return $user->can('supplychain.view') && ((int) $item->company_id === (int) $user->company_id);
    }

    public function create(User $user): bool
    {
        return $user->can('supplychain.manage');
    }

    public function update(User $user, Item $item): bool
    {
        return $user->can('supplychain.manage') && ((int) $item->company_id === (int) $user->company_id);
    }

    public function delete(User $user, Item $item): bool
    {
        return $this->update($user, $item);
    }
}
