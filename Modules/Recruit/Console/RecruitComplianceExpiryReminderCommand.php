<?php

namespace Modules\Recruit\Console;

use App\Models\EmployeeDetails;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Recruit\Notifications\RecruitComplianceExpiryNotification;

/**
 * Sends expiry alert notifications to employees whose compliance fields
 * (Police Check, WWCC, Insurance) are due to expire within the configured
 * thresholds.
 *
 * Schedule this command daily:
 *   php artisan recruit:compliance-expiry-reminder
 *
 * Alert thresholds default to [30, 14, 7] days before expiry.
 */
class RecruitComplianceExpiryReminderCommand extends Command
{
    protected $signature = 'recruit:compliance-expiry-reminder';

    protected $description = 'Send compliance expiry reminders (police check, WWCC, insurance) for field workers';

    /** Compliance fields to check mapped to a human-readable label. */
    private const EXPIRY_FIELDS = [
        'police_check_expiry' => 'Police Check',
        'wwcc_expiry'         => 'Working With Children Check (WWCC)',
        'insurance_expiry'    => 'Insurance',
    ];

    public function handle(): int
    {
        $alertDays = config('recruit.compliance_expiry_alert_days', [30, 14, 7]);

        $sent = 0;

        foreach ($alertDays as $days) {
            $targetDate = Carbon::today()->addDays($days)->toDateString();

            foreach (self::EXPIRY_FIELDS as $field => $label) {
                $records = EmployeeDetails::with('user')
                    ->whereNotNull($field)
                    ->whereDate($field, $targetDate)
                    ->get();

                foreach ($records as $detail) {
                    $user = $detail->user;

                    if (!$user) {
                        continue;
                    }

                    try {
                        $user->notify(new RecruitComplianceExpiryNotification($field, $label, $targetDate, $days));
                        $sent++;
                        $this->line("  → Alert sent to {$user->name} — {$label} expires in {$days} day(s) ({$targetDate})");
                    } catch (\Throwable $e) {
                        $this->warn("  ✗ Failed to notify {$user->name}: {$e->getMessage()}");
                    }
                }
            }
        }

        $this->info("Recruit compliance expiry reminders sent: {$sent}");

        return self::SUCCESS;
    }
}
