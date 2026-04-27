<?php

namespace Modules\CleanQuality\Policies;

use App\Models\User;
use Modules\CleanQuality\Entities\Inspection;
use Modules\CleanQuality\Support\InspectionPermissions;

class InspectionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(InspectionPermissions::VIEW)
            || $user->hasPermissionTo(InspectionPermissions::LEGACY_VIEW);
    }

    public function view(User $user, Inspection $inspection): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(InspectionPermissions::CREATE)
            || $user->hasPermissionTo(InspectionPermissions::LEGACY_CREATE);
    }

    public function update(User $user, Inspection $inspection): bool
    {
        return $user->hasPermissionTo(InspectionPermissions::UPDATE)
            || $user->hasPermissionTo(InspectionPermissions::LEGACY_UPDATE);
    }

    public function delete(User $user, Inspection $inspection): bool
    {
        return $user->hasPermissionTo(InspectionPermissions::DELETE)
            || $user->hasPermissionTo(InspectionPermissions::LEGACY_DELETE);
    }
}

