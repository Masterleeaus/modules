<?php

namespace App\Console\Commands\Modules;

use App\Modules\ModuleInstaller;
use Illuminate\Console\Command;

class UpgradeModuleCommand extends Command
{
    protected $signature = 'titan:module:upgrade
                            {module : The module ID to upgrade}
                            {--from= : Current version (reads from module_installations if omitted)}
                            {--to=   : Target version (reads from module.json if omitted)}';

    protected $description = 'Run upgrade hooks for a module to transition it between versions.';

    public function handle(ModuleInstaller $installer): int
    {
        $moduleId = $this->argument('module');

        $fromVersion = $this->option('from') ?? $this->resolveInstalledVersion($moduleId);
        $toVersion   = $this->option('to')   ?? $this->resolveManifestVersion($moduleId);

        if (! $fromVersion || ! $toVersion) {
            $this->error("Could not determine version(s) for {$moduleId}. Use --from and --to.");

            return self::FAILURE;
        }

        $this->info("Upgrading module {$moduleId}: {$fromVersion} → {$toVersion}");

        $result = $installer->upgrade($moduleId, $fromVersion, $toVersion);

        if (! $result->success) {
            $this->error($result->message);

            return self::FAILURE;
        }

        $this->info($result->message);

        return self::SUCCESS;
    }

    private function resolveInstalledVersion(string $moduleId): ?string
    {
        $record = \App\Models\ModuleInstallation::where('module_id', $moduleId)->first();

        return $record?->version;
    }

    private function resolveManifestVersion(string $moduleId): ?string
    {
        $path = base_path("Modules/{$moduleId}/module.json");
        if (! file_exists($path)) {
            return null;
        }
        $manifest = json_decode(file_get_contents($path), true);

        return isset($manifest['version']) ? (string) $manifest['version'] : null;
    }
}
