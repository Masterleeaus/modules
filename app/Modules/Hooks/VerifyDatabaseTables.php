<?php

namespace App\Modules\Hooks;

use App\Modules\HookResult;
use Illuminate\Support\Facades\Schema;

/**
 * Verify that the database tables created by the module's migrations actually
 * exist. This acts as a smoke test after RunMigrations.
 *
 * Tables are discovered by scanning the migration files for `Schema::create`
 * calls — a lightweight, regex-based heuristic.
 */
class VerifyDatabaseTables extends BaseHook
{
    public function handle(string $moduleId, array &$context): HookResult
    {
        $paths = $this->migrationPaths($moduleId);

        if (empty($paths)) {
            return HookResult::ok("No migrations directory for {$moduleId} — skipping table verification.");
        }

        $tables = $this->discoverTables($paths);

        if (empty($tables)) {
            return HookResult::ok("No tables to verify for {$moduleId}.");
        }

        $missing = [];
        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                $missing[] = $table;
            }
        }

        if (! empty($missing)) {
            return HookResult::fail(
                "Missing table(s) for {$moduleId}: " . implode(', ', $missing),
                ['missing_tables' => $missing],
            );
        }

        return HookResult::ok(
            "All " . count($tables) . " table(s) verified for {$moduleId}.",
            ['tables' => $tables],
        );
    }

    /** @return string[] */
    private function discoverTables(array $paths): array
    {
        $tables = [];

        foreach ($paths as $dir) {
            foreach (glob("{$dir}/*.php") ?: [] as $file) {
                $content = file_get_contents($file);
                if (preg_match_all('/Schema::create\s*\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
                    foreach ($matches[1] as $table) {
                        $tables[] = $table;
                    }
                }
            }
        }

        return array_unique($tables);
    }
}
