<?php

namespace App\Console\Commands\Modules;

use App\Models\ModuleInstallation;
use App\Modules\ModuleInstaller;
use Illuminate\Console\Command;

class VerifyModuleCommand extends Command
{
    protected $signature = 'titan:module:verify
                            {module? : The module ID to verify (omit with --all to verify every module)}
                            {--all : Verify every module discovered on disk}';

    protected $description = 'Run post-install health checks for one or all modules. Exits non-zero if any module is unhealthy.';

    public function handle(ModuleInstaller $installer): int
    {
        if ($this->option('all')) {
            return $this->verifyAll($installer);
        }

        $moduleId = $this->argument('module');

        if (! $moduleId) {
            $this->error('Provide a module ID or use --all.');

            return self::FAILURE;
        }

        return $this->verifySingle($installer, $moduleId);
    }

    private function verifySingle(ModuleInstaller $installer, string $moduleId): int
    {
        $this->info("Verifying module: {$moduleId}");

        $result = $installer->verify($moduleId);

        if ($result->healthy) {
            $this->info($result->message);
        } else {
            $this->error($result->message);
        }

        foreach ($result->checks as $hookName => $check) {
            $icon = $check['passed'] ? '<info>✓</info>' : '<error>✗</error>';
            $this->line("  {$icon} {$hookName}: {$check['message']}");
        }

        return $result->healthy ? self::SUCCESS : self::FAILURE;
    }

    private function verifyAll(ModuleInstaller $installer): int
    {
        $modules = $installer->discoverModules();

        if (empty($modules)) {
            $this->warn('No modules found on disk.');

            return self::SUCCESS;
        }

        $this->info('Verifying ' . count($modules) . ' module(s)...');
        $this->newLine();

        $failed = [];

        foreach ($modules as $moduleId) {
            $result = $installer->verify($moduleId);
            $icon   = $result->healthy ? '<info>✓</info>' : '<error>✗</error>';
            $this->line("  {$icon} {$moduleId}: {$result->message}");

            if (! $result->healthy) {
                $failed[] = $moduleId;
            }
        }

        $this->newLine();

        if (! empty($failed)) {
            $this->error('Unhealthy module(s): ' . implode(', ', $failed));

            return self::FAILURE;
        }

        $this->info('All modules healthy.');

        return self::SUCCESS;
    }
}
