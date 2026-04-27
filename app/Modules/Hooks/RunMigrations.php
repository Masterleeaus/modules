<?php

namespace App\Modules\Hooks;

use App\Modules\HookResult;

/**
 * Run database migrations for the module.
 *
 * This hook is idempotent: Laravel tracks which migrations have already been
 * run in the `migrations` table, so calling migrate twice is always safe.
 */
class RunMigrations extends BaseHook
{
    public function handle(string $moduleId, array &$context): HookResult
    {
        $paths = $this->migrationPaths($moduleId);

        if (empty($paths)) {
            return HookResult::ok("No migration directory found for {$moduleId} — skipping.");
        }

        $ran = [];
        foreach ($paths as $path) {
            $this->artisan('migrate', [
                '--path' => $this->relativeMigrationPath($path),
                '--force' => true,
            ]);
            $ran[] = $path;
        }

        return HookResult::ok("Migrations run for {$moduleId}.", ['paths' => $ran]);
    }

    private function relativeMigrationPath(string $absolutePath): string
    {
        return str_replace(base_path() . '/', '', $absolutePath);
    }
}
