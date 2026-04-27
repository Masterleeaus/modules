<?php

namespace Modules\PMCore\app\Console;

use Illuminate\Console\Command;
use Modules\PMCore\app\Services\RecurrenceService;

class GenerateRecurringJobs extends Command
{
    protected $signature = 'pmcore:generate-recurring-jobs
                            {--dry-run : Preview which jobs would be created without saving}';

    protected $description = 'Generate child cleaning jobs for all active recurring projects that are due.';

    public function __construct(private RecurrenceService $recurrenceService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if ($this->option('dry-run')) {
            $this->info('[Dry run] No jobs will be persisted.');
        }

        $this->info('Processing recurring cleaning jobs…');

        if ($this->option('dry-run')) {
            $this->warn('Dry-run mode is active — skipping actual generation.');

            return self::SUCCESS;
        }

        $count = $this->recurrenceService->processRecurringJobs();

        $this->info("Done. {$count} recurring job(s) generated.");

        return self::SUCCESS;
    }
}
