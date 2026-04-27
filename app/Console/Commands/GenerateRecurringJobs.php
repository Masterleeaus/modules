<?php

namespace App\Console\Commands;

use App\Models\Job;
use App\Models\RecurringJobTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerateRecurringJobs extends Command
{
    protected $signature   = 'jobs:generate-recurring {--days=7 : How many days ahead to generate jobs for}';
    protected $description = 'Generate scheduled jobs from active recurring templates.';

    public function handle(): int
    {
        $horizon = Carbon::today()->addDays((int) $this->option('days'));

        $templates = RecurringJobTemplate::where('is_active', true)
            ->where('start_date', '<=', $horizon)
            ->where(function ($q) use ($horizon) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', Carbon::today());
            })
            ->with(['customer', 'property', 'jobType'])
            ->get();

        $created = 0;

        foreach ($templates as $template) {
            $nextDate = $template->nextRunDate();

            if (! $nextDate || $nextDate->isAfter($horizon)) {
                continue;
            }

            // Avoid duplicate jobs on the same date for the same template
            $exists = Job::where('recurring_template_id', $template->id)
                ->whereDate('scheduled_at', $nextDate)
                ->exists();

            if ($exists) {
                continue;
            }

            Job::create([
                'organization_id'      => $template->organization_id,
                'customer_id'          => $template->customer_id,
                'property_id'          => $template->property_id,
                'job_type_id'          => $template->job_type_id,
                'assigned_to'          => $template->assigned_to,
                'recurring_template_id' => $template->id,
                'title'                => $template->title,
                'description'          => $template->description,
                'status'               => Job::STATUS_SCHEDULED,
                'scheduled_at'         => $nextDate->setTimeFromTimeString('09:00:00'),
            ]);

            $template->update(['last_generated_on' => $nextDate]);
            $created++;
        }

        $this->info("Generated {$created} recurring job(s).");

        return self::SUCCESS;
    }
}
