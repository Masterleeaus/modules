<?php

namespace Modules\Purchase\Notifications;

use App\Models\Currency;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VendorCreditPaymentMade extends Notification
{
    use Queueable;

    protected $totalPaid;
    protected $remainingAmount;

    /**
     * Create a new notification instance.
     */
    public function __construct($totalPaid, $remainingAmount)
    {
        $this->totalPaid = $totalPaid;
        $this->remainingAmount = $remainingAmount;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $currency = Currency::where('id', $notifiable->currency_id)->where('company_id', company()->id)->first();

        return (new MailMessage)
            ->subject(__('purchase::messages.paymentConfirmation'))
            ->greeting( $notifiable->primary_name . ' !')
            ->line(__('purchase::messages.messagePayment') . $currency->currency_symbol . $this->totalPaid)
            ->line(__('purchase::messages.remainingCreditAmount') . '' . $currency->currency_symbol . $this->remainingAmount)
            ->line(__('purchase::messages.thankYou'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [];
    }
}
