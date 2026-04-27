<?php

namespace App\Modules\Hooks;

use App\Modules\HookResult;

/**
 * Register (seed) permissions declared in the module's module.json.
 *
 * Uses `firstOrCreate` via spatie/laravel-permission so this hook is
 * idempotent and safe to re-run.
 */
class RegisterPermissions extends BaseHook
{
    public function handle(string $moduleId, array &$context): HookResult
    {
        $manifestPath = $this->modulePath($moduleId, 'module.json');

        if (! file_exists($manifestPath)) {
            return HookResult::ok("No module.json found for {$moduleId} — skipping permission registration.");
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);
        $permissions = $manifest['permissions'] ?? [];

        if (empty($permissions)) {
            return HookResult::ok("No permissions declared in module.json for {$moduleId}.");
        }

        if (! class_exists(\Spatie\Permission\Models\Permission::class)) {
            return HookResult::fail('spatie/laravel-permission is not installed.');
        }

        $created = [];
        foreach ($permissions as $name) {
            \Spatie\Permission\Models\Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
            );
            $created[] = $name;
        }

        return HookResult::ok(
            "Registered " . count($created) . " permission(s) for {$moduleId}.",
            ['permissions' => $created],
        );
    }
}
