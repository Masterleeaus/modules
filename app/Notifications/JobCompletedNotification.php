<?php

namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Job $job) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("{$this->job->title} — Job Completed")
            ->greeting("Hi {$notifiable->first_name},")
            ->line("Your job **{$this->job->title}** has been completed.")
            ->line('Thank you for choosing us. Please reach out if you have any questions or feedback.');
    }
}
