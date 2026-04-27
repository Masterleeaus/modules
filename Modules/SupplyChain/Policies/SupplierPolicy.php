<?php

namespace Modules\SupplyChain\Policies;

use App\Models\User;
use Modules\SupplyChain\Entities\Supplier;

class SupplierPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('supplychain.suppliers.view');
    }

    public function view(User $user, Supplier $supplier): bool
    {
        return $user->can('supplychain.suppliers.view') && ((int) $supplier->company_id === (int) $user->company_id);
    }

    public function create(User $user): bool
    {
        return $user->can('supplychain.suppliers.manage');
    }

    public function update(User $user, Supplier $supplier): bool
    {
        return $user->can('supplychain.suppliers.manage') && ((int) $supplier->company_id === (int) $user->company_id);
    }

    public function delete(User $user, Supplier $supplier): bool
    {
        return $this->update($user, $supplier);
    }
}
