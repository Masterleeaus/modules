<?php

namespace App\Modules\Hooks;

use App\Modules\HookResult;

/**
 * Verify that the permissions declared in module.json have been registered in
 * the database (i.e. the RegisterPermissions hook ran successfully).
 */
class VerifyPermissionsRegistered extends BaseHook
{
    public function handle(string $moduleId, array &$context): HookResult
    {
        $manifestPath = $this->modulePath($moduleId, 'module.json');

        if (! file_exists($manifestPath)) {
            return HookResult::ok("No module.json for {$moduleId} — skipping permission verification.");
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);
        $expected = $manifest['permissions'] ?? [];

        if (empty($expected)) {
            return HookResult::ok("No permissions declared for {$moduleId}.");
        }

        if (! class_exists(\Spatie\Permission\Models\Permission::class)) {
            return HookResult::fail('spatie/laravel-permission is not installed — cannot verify permissions.');
        }

        $missing = [];
        foreach ($expected as $name) {
            if (! \Spatie\Permission\Models\Permission::where('name', $name)->exists()) {
                $missing[] = $name;
            }
        }

        if (! empty($missing)) {
            return HookResult::fail(
                "Missing permission(s) for {$moduleId}: " . implode(', ', $missing),
                ['missing_permissions' => $missing],
            );
        }

        return HookResult::ok(
            "All " . count($expected) . " permission(s) verified for {$moduleId}.",
            ['permissions' => $expected],
        );
    }
}
