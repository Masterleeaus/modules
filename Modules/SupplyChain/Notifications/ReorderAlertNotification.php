<?php

namespace Modules\SupplyChain\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReorderAlertNotification extends Notification
{
    use Queueable;

    public function __construct(public array $payload)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $itemName      = $this->payload['item_name']      ?? ('Item #' . ($this->payload['item_id'] ?? 'n/a'));
        $warehouseName = $this->payload['warehouse_name'] ?? ('Warehouse #' . ($this->payload['warehouse_id'] ?? 'n/a'));
        $available     = $this->payload['qty_available']  ?? 0;
        $minimum       = $this->payload['min_qty']        ?? 0;

        return (new MailMessage())
            ->subject('⚠️ Low Stock Alert: ' . $itemName)
            ->greeting('Low Stock Alert')
            ->line("**{$itemName}** at **{$warehouseName}** has fallen below the minimum quantity threshold.")
            ->line("Available: **{$available}** | Minimum required: **{$minimum}**")
            ->line('Please raise a purchase order to replenish stock.')
            ->action('Open Supply Chain', url('/admin/supply-chain/purchase-orders/create'));
    }
}
