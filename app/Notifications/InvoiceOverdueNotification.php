<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceOverdueNotification extends Notification
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
            ->subject("Overdue: Invoice #{$this->invoice->invoice_number}")
            ->greeting("Hi {$notifiable->first_name},")
            ->line("Invoice **#{$this->invoice->invoice_number}** is now overdue.")
            ->line('Please arrange payment as soon as possible to avoid any service interruption.')
            ->line('If you have already made payment, please disregard this notice.');
    }
}
