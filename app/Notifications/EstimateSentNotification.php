<?php

namespace App\Notifications;

use App\Models\Estimate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EstimateSentNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Estimate $estimate) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Your estimate \"{$this->estimate->title}\" is ready")
            ->greeting("Hi {$notifiable->first_name},")
            ->line("Your estimate **{$this->estimate->title}** has been sent and is ready for your review.")
            ->action('View Estimate', route('estimates.public', $this->estimate->token))
            ->line('Please let us know if you have any questions.');
    }
}
