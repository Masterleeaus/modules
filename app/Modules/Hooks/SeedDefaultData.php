<?php

namespace App\Modules\Hooks;

use App\Modules\HookResult;

/**
 * Run module-specific database seeders.
 *
 * Seeders must use `firstOrCreate` / `updateOrCreate` internally so this hook
 * is safe to call multiple times without duplicating data.
 *
 * Convention: a seeder class named `Modules\{ModuleId}\Database\Seeders\{ModuleId}DatabaseSeeder`
 * will be executed when present.
 */
class SeedDefaultData extends BaseHook
{
    public function handle(string $moduleId, array &$context): HookResult
    {
        $candidates = [
            "Modules\\{$moduleId}\\Database\\Seeders\\{$moduleId}DatabaseSeeder",
            "Modules\\{$moduleId}\\Database\\Seeders\\DatabaseSeeder",
        ];

        foreach ($candidates as $seederClass) {
            if (class_exists($seederClass)) {
                $this->artisan('db:seed', [
                    '--class' => $seederClass,
                    '--force' => true,
                ]);

                return HookResult::ok("Seeder {$seederClass} executed for {$moduleId}.");
            }
        }

        return HookResult::ok("No seeder found for {$moduleId} — skipping.");
    }
}
