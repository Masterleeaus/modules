<?php

namespace Modules\CleanQuality\Policies;

use App\Models\User;
use Modules\CleanQuality\Entities\QcRecord;
use Modules\CleanQuality\Support\InspectionPermissions;

/**
 * Policy for QcRecord model.
 *
 * QC Records are write-initiated by automation (ScoreQualityCheck action / QC jobs),
 * so create/update/delete are guarded by the same UPDATE permission as inspections.
 * Viewers need the standard VIEW permission.
 */
class QcRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(InspectionPermissions::VIEW)
            || $user->hasPermissionTo(InspectionPermissions::LEGACY_VIEW);
    }

    public function view(User $user, QcRecord $record): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(InspectionPermissions::CREATE)
            || $user->hasPermissionTo(InspectionPermissions::LEGACY_CREATE);
    }

    public function update(User $user, QcRecord $record): bool
    {
        return $user->hasPermissionTo(InspectionPermissions::UPDATE)
            || $user->hasPermissionTo(InspectionPermissions::LEGACY_UPDATE);
    }

    public function delete(User $user, QcRecord $record): bool
    {
        return $user->hasPermissionTo(InspectionPermissions::DELETE)
            || $user->hasPermissionTo(InspectionPermissions::LEGACY_DELETE);
    }
}
