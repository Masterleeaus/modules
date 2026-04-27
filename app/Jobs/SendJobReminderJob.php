<?php

namespace App\Jobs;

use App\Models\Job;
use App\Models\MessageTemplate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendJobReminderJob implements ShouldQueue
{
    use Queueable;

    const TYPE_24H = '24h';
    const TYPE_2H  = '2h';

    public function __construct(
        public readonly Job    $job,
        public readonly string $reminderType, // '24h' or '2h'
    ) {}

    public function handle(): void
    {
        $customer = $this->job->customer;

        if (! $customer) {
            return;
        }

        $preference = $customer->reminder_preference ?? 'email';

        if ($preference === 'none') {
            $this->markSent();
            return;
        }

        $subject = $this->reminderType === self::TYPE_24H
            ? 'Your appointment is tomorrow'
            : 'Your appointment is in 2 hours';

        $body = $this->buildBody();

        // Send email if applicable
        if (in_array($preference, ['email', 'both'], true) && $customer->email) {
            try {
                Mail::raw($body, function ($message) use ($customer, $subject) {
                    $message->to($customer->email, $customer->full_name)
                            ->subject($subject);
                });
            } catch (\Throwable $e) {
                Log::warning("Job reminder email failed for job {$this->job->id}: {$e->getMessage()}");
            }
        }

        $this->markSent();
    }

    private function buildBody(): string
    {
        $scheduledAt = $this->job->scheduled_at?->format('l, F j \a\t g:i A') ?? 'soon';
        $address     = $this->job->property?->full_address ?? '';

        $intro = $this->reminderType === self::TYPE_24H
            ? "This is a reminder that your appointment is scheduled for tomorrow, {$scheduledAt}."
            : "This is a reminder that your appointment is in approximately 2 hours ({$scheduledAt}).";

        return implode("\n\n", array_filter([
            "Hi {$this->job->customer?->first_name},",
            $intro,
            $address ? "Location: {$address}" : null,
            "If you need to reschedule or have questions, please contact us.",
            "Thank you!",
        ]));
    }

    private function markSent(): void
    {
        $column = $this->reminderType === self::TYPE_24H
            ? 'reminder_sent_24h_at'
            : 'reminder_sent_2h_at';

        $this->job->update([$column => now()]);
    }
}
