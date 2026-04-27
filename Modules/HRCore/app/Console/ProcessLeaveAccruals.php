<?php

namespace Modules\HRCore\app\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\HRCore\app\Models\LeaveAccrual;

class ProcessLeaveAccruals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hrcore:process-leave-accruals {--date= : Process accruals for a specific date (Y-m-d)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process leave accruals for all employees based on leave type settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::now();

        $this->info('Processing leave accruals for date: '.$date->format('Y-m-d'));

        try {
            $processedCount = LeaveAccrual::processAccruals($date);

            $this->info("Successfully processed {$processedCount} accrual entries.");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error processing accruals: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
