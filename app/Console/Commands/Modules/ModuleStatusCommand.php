<?php

namespace App\Console\Commands\Modules;

use App\Models\ModuleInstallation;
use App\Modules\ModuleInstaller;
use Illuminate\Console\Command;

class ModuleStatusCommand extends Command
{
    protected $signature = 'titan:module:status';

    protected $description = 'Show the installation status of all modules.';

    public function handle(ModuleInstaller $installer): int
    {
        $installations = $installer->allInstallations();
        $discovered    = $installer->discoverModules();

        if (empty($discovered)) {
            $this->warn('No modules found on disk.');

            return self::SUCCESS;
        }

        $indexed = [];
        foreach ($installations as $record) {
            $indexed[$record->module_id] = $record;
        }

        $rows = [];
        foreach ($discovered as $moduleId) {
            $record = $indexed[$moduleId] ?? null;
            $rows[] = [
                $moduleId,
                $record?->version ?? '—',
                $record?->status ?? '<comment>not installed</comment>',
                $record?->installed_at?->toDateTimeString() ?? '—',
                $record?->last_upgraded_at?->toDateTimeString() ?? '—',
            ];
        }

        $this->table(
            ['Module', 'Version', 'Status', 'Installed At', 'Last Upgraded'],
            $rows,
        );

        return self::SUCCESS;
    }
}
