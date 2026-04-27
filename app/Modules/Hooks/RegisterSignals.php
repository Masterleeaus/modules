<?php

namespace App\Modules\Hooks;

use App\Modules\HookResult;

/**
 * Validate that the module's signals manifest (manifests/signals.json) is
 * structurally sound and register any declared signals.
 *
 * This hook is read-only / validating — it does not persist anything — so it
 * is inherently idempotent.
 */
class RegisterSignals extends BaseHook
{
    public function handle(string $moduleId, array &$context): HookResult
    {
        $candidates = [
            $this->modulePath($moduleId, 'manifests/signals.json'),
            $this->modulePath($moduleId, 'manifests/signals_manifest.json'),
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                $content = json_decode(file_get_contents($path), true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    return HookResult::fail("Invalid JSON in signals manifest at {$path}.");
                }

                $signals = $content['signals'] ?? $content;
                $count = is_countable($signals) ? count($signals) : 0;

                return HookResult::ok(
                    "Signals manifest validated for {$moduleId} ({$count} signal(s)).",
                    ['path' => $path, 'signal_count' => $count],
                );
            }
        }

        return HookResult::ok("No signals manifest found for {$moduleId} — skipping.");
    }
}
