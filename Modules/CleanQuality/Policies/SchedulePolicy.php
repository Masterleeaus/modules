<?php

namespace Modules\CleanQuality\Policies;

use App\Models\User;
use Modules\CleanQuality\Support\InspectionPermissions;

class SchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(InspectionPermissions::VIEW)
            || $user->hasPermissionTo(InspectionPermissions::LEGACY_VIEW);
    }
}
