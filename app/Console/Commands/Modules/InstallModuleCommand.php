<?php

namespace App\Console\Commands\Modules;

use App\Modules\ModuleInstaller;
use Illuminate\Console\Command;

class InstallModuleCommand extends Command
{
    protected $signature = 'titan:module:install
                            {module : The module ID to install (matches the folder name under Modules/)}
                            {--force : Re-run install hooks even if the module is already installed}';

    protected $description = 'Install a module: run hooks, seed data, register permissions, and verify health.';

    public function handle(ModuleInstaller $installer): int
    {
        $moduleId = $this->argument('module');
        $force    = $this->option('force');

        if ($force) {
            \App\Models\ModuleInstallation::where('module_id', $moduleId)->delete();
        }

        $this->info("Installing module: {$moduleId}");

        $result = $installer->install($moduleId);

        if (! $result->success) {
            $this->error($result->message);
            if ($result->failedHook) {
                $this->line("  Failed hook: <comment>{$result->failedHook}</comment>");
            }
            $this->printHookResults($result->hookResults);

            return self::FAILURE;
        }

        if (str_contains($result->message, 'already installed')) {
            $this->warn($result->message);

            return self::SUCCESS;
        }

        $this->info($result->message);
        $this->printHookResults($result->hookResults);

        return self::SUCCESS;
    }

    private function printHookResults(array $hookResults): void
    {
        if (empty($hookResults)) {
            return;
        }

        $this->newLine();
        $this->line('  Hook results:');
        foreach ($hookResults as $name => $result) {
            $icon   = $result->success ? '<info>✓</info>' : '<error>✗</error>';
            $status = $result->success ? 'passed' : 'failed';
            $this->line("    {$icon} [{$status}] {$name}: {$result->message}");
        }
    }
}
