<?php

namespace Modules\Purchase\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Sent when a purchase order is dispatched to the supplier.
 */
class PurchaseOrderSentNotification extends Notification
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
            ->subject("Purchase Order #{$this->order->po_number} Sent")
            ->greeting('Hello!')
            ->line("Purchase order **#{$this->order->po_number}** has been sent to the supplier.")
            ->action('View Purchase Order', url('/purchase/orders/' . $this->order->id))
            ->line('You will be notified when goods are received or an invoice is matched.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'      => 'purchase_order_sent',
            'order_id'  => $this->order->id,
            'po_number' => $this->order->po_number,
        ];
    }
}
