<?php

namespace Modules\Recruit\Console;

use Illuminate\Console\Command;
use Modules\Recruit\Entities\RecruitJob;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class JobEndDateCheck extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job-end-date-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check end date of job and set it close after date specified in database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        RecruitJob::whereNotNull('end_date')->where('end_date', '<', now()->format('Y-m-d H:i:s'))->update(['status' => 'closed']);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }

}
