<?php

namespace Modules\Purchase\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Sent to the purchasing manager when a product stock falls below the reorder
 * point and a new reorder suggestion (draft PO) has been auto-created.
 */
class ReorderSuggestionNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Order $order,
        private readonly string $productName,
        private readonly int    $currentStock,
        private readonly int    $reorderPoint
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reorder Suggestion: ' . $this->productName)
            ->greeting('Hello!')
            ->line("Stock for **{$this->productName}** has fallen to {$this->currentStock} units (reorder point: {$this->reorderPoint}).")
            ->line('A draft purchase order has been created automatically.')
            ->action('Review Draft PO', url('/purchase/orders/' . $this->order->id))
            ->line('Please review and send to the supplier.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'reorder_suggestion',
            'order_id'      => $this->order->id,
            'product_name'  => $this->productName,
            'current_stock' => $this->currentStock,
            'reorder_point' => $this->reorderPoint,
        ];
    }
}
