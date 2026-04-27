<?php

namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobReminderNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Job $job) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $scheduledAt = $this->job->scheduled_at?->format('l, F j \a\t g:i A');

        return (new MailMessage)
            ->subject("Reminder: {$this->job->title} scheduled for {$scheduledAt}")
            ->greeting("Hi {$notifiable->first_name},")
            ->line("This is a reminder that **{$this->job->title}** is scheduled for **{$scheduledAt}**.")
            ->line('Our team will be in touch shortly before the appointment.');
    }
}
