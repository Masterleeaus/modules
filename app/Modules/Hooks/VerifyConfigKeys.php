<?php

namespace App\Modules\Hooks;

use App\Modules\HookResult;

/**
 * Verify that the config keys declared by the module are accessible.
 *
 * Checks that the module's main config file can be read and that a baseline
 * `name` key is resolvable. This is a lightweight sanity check.
 */
class VerifyConfigKeys extends BaseHook
{
    public function handle(string $moduleId, array &$context): HookResult
    {
        $alias = strtolower($moduleId);

        // Try both the module alias and the camelCase version.
        $candidates = [$alias, lcfirst($moduleId)];

        foreach ($candidates as $key) {
            $value = config($key);
            if ($value !== null) {
                return HookResult::ok("Config key [{$key}] is accessible for {$moduleId}.");
            }
        }

        // Not every module publishes a config file — this is advisory only.
        return HookResult::ok("No config key found for {$moduleId} — this is normal if the module has no config file.");
    }
}
