<?php

use App\Console\Commands\DispatchJobReminders;
use App\Console\Commands\GenerateRecurringJobs;
use App\Console\Commands\PruneDriverLocations;
use App\Console\Commands\SendInvoiceReminders;
use App\Console\Commands\SendJobReminders;
use App\Console\Commands\SendTrialEndingReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(SendInvoiceReminders::class)->dailyAt('08:00');
Schedule::command(SendJobReminders::class)->hourly();
Schedule::command(PruneDriverLocations::class)->dailyAt('03:00');

// Send trial-ending reminders 3 days before expiry, daily at 09:00
Schedule::command(SendTrialEndingReminders::class, ['--days=3'])->dailyAt('09:00');

// Tracked reminder system (prevents duplicate sends via DB columns)
Schedule::command(DispatchJobReminders::class)->everyThirtyMinutes();

// Generate recurring jobs for the next 7 days every morning
Schedule::command(GenerateRecurringJobs::class)->dailyAt('00:30');
