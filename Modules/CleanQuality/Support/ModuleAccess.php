<?php

namespace Modules\CleanQuality\Support;

use App\Models\User;

final class ModuleAccess
{
    public const MODULE_ALIASES = ['quality_control', 'inspections'];

    public static function userHasModule(?User $user = null): bool
    {
        $user ??= user();

        if (! $user) {
            return false;
        }

        $modules = $user->modules ?? user_modules();

        if (is_string($modules)) {
            $modules = array_filter(array_map('trim', explode(',', $modules)));
        }

        if ($modules instanceof \Illuminate\Support\Collection) {
            $modules = $modules->all();
        }

        if (! is_array($modules)) {
            return false;
        }

        return count(array_intersect(self::MODULE_ALIASES, $modules)) > 0;
    }

    public static function permissionLevel(string $permission, ?User $user = null): string
    {
        $user ??= user();

        if (! $user) {
            return 'none';
        }

        $level = $user->permission($permission);

        if ($level !== 'none') {
            return $level;
        }

        $legacy = InspectionPermissions::legacyFor($permission);

        if (! $legacy) {
            return $level;
        }

        return $user->hasPermissionTo($legacy) ? 'all' : 'none';
    }

    public static function can(string $permission, array $allowed = ['all'], ?User $user = null): bool
    {
        return in_array(self::permissionLevel($permission, $user), $allowed, true);
    }
}
