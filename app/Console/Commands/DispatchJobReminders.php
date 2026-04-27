<?php

namespace App\Console\Commands;

use App\Jobs\SendJobReminderJob;
use App\Models\Job;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class DispatchJobReminders extends Command
{
    protected $signature   = 'jobs:dispatch-reminders';
    protected $description = 'Dispatch 24-hour and 2-hour appointment reminder notifications with tracking (prevents duplicate sends).';

    public function handle(): int
    {
        $now = Carbon::now();

        // 24-hour window: jobs scheduled between 23h and 25h from now
        $jobs24h = Job::whereNull('reminder_sent_24h_at')
            ->whereNotIn('status', [Job::STATUS_CANCELLED, Job::STATUS_COMPLETED])
            ->whereNotNull('scheduled_at')
            ->whereBetween('scheduled_at', [
                $now->copy()->addHours(23),
                $now->copy()->addHours(25),
            ])
            ->with(['customer', 'property'])
            ->get();

        foreach ($jobs24h as $job) {
            SendJobReminderJob::dispatch($job, SendJobReminderJob::TYPE_24H);
        }

        // 2-hour window: jobs scheduled between 1h45m and 2h15m from now
        $jobs2h = Job::whereNull('reminder_sent_2h_at')
            ->whereNotIn('status', [Job::STATUS_CANCELLED, Job::STATUS_COMPLETED])
            ->whereNotNull('scheduled_at')
            ->whereBetween('scheduled_at', [
                $now->copy()->addMinutes(105),
                $now->copy()->addMinutes(135),
            ])
            ->with(['customer', 'property'])
            ->get();

        foreach ($jobs2h as $job) {
            SendJobReminderJob::dispatch($job, SendJobReminderJob::TYPE_2H);
        }

        $this->info("Dispatched {$jobs24h->count()} 24h reminders and {$jobs2h->count()} 2h reminders.");

        return self::SUCCESS;
    }
}
