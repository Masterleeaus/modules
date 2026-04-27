<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceSentNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Invoice $invoice) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Invoice #{$this->invoice->invoice_number} from {$this->invoice->organization->name}")
            ->greeting("Hi {$notifiable->first_name},")
            ->line("Invoice **#{$this->invoice->invoice_number}** has been sent to you.")
            ->line('Please review and process payment at your earliest convenience.')
            ->line('Thank you for your business.');
    }
}
