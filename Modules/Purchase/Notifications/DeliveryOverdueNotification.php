<?php

namespace Modules\Purchase\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Sent when a purchase order's expected delivery date has passed and the
 * goods have not yet been received (purchase_status != 'received').
 */
class DeliveryOverdueNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Order $order
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("⚠️ Delivery Overdue: PO #{$this->order->po_number}")
            ->greeting('Action Required')
            ->line("Purchase order **#{$this->order->po_number}** was expected on **{$this->order->expected_delivery_date}** but goods have not yet been received.")
            ->action('View Purchase Order', url('/purchase/orders/' . $this->order->id))
            ->line('Please follow up with the supplier.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'                    => 'delivery_overdue',
            'order_id'                => $this->order->id,
            'po_number'               => $this->order->po_number,
            'expected_delivery_date'  => (string) $this->order->expected_delivery_date,
        ];
    }
}
