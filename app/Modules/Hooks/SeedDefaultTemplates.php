<?php

namespace App\Modules\Hooks;

use App\Modules\HookResult;

/**
 * Seed default message/notification templates for the module if a dedicated
 * template seeder is present.
 *
 * Convention: `Modules\{ModuleId}\Database\Seeders\TemplateSeeder`
 */
class SeedDefaultTemplates extends BaseHook
{
    public function handle(string $moduleId, array &$context): HookResult
    {
        $seederClass = "Modules\\{$moduleId}\\Database\\Seeders\\TemplateSeeder";

        if (! class_exists($seederClass)) {
            return HookResult::ok("No template seeder found for {$moduleId} — skipping.");
        }

        $this->artisan('db:seed', [
            '--class' => $seederClass,
            '--force' => true,
        ]);

        return HookResult::ok("Template seeder executed for {$moduleId}.");
    }
}
