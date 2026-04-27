<?php

namespace Modules\Purchase\Notifications;

use App\Models\Expense;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Sent to the finance team when a supplier invoice is successfully matched to a
 * purchase order and an Expense record has been auto-created.
 */
class InvoiceMatchedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Order   $order,
        private readonly Expense $expense
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Invoice Matched: PO #{$this->order->po_number}")
            ->greeting('Invoice Matched!')
            ->line("Supplier invoice **{$this->order->invoice_reference}** has been matched to purchase order **#{$this->order->po_number}**.")
            ->line("An expense record (#{$this->expense->id}) has been created automatically for \${$this->expense->total}.")
            ->action('View Expense', url('/expenses/' . $this->expense->id))
            ->line('No further action required unless reconciliation is needed.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'              => 'invoice_matched',
            'order_id'          => $this->order->id,
            'po_number'         => $this->order->po_number,
            'invoice_reference' => $this->order->invoice_reference,
            'expense_id'        => $this->expense->id,
        ];
    }
}
