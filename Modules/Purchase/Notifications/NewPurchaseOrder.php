<?php

namespace Modules\Purchase\Notifications;

use App\Models\Invoice;
use App\Models\EmailNotificationSetting;
use App\Http\Controllers\InvoiceController;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\SlackMessage;
use Modules\Purchase\Entities\PurchaseNotificationSetting;
use Modules\Purchase\Entities\PurchaseOrder;
use Modules\Purchase\Http\Controllers\PurchaseOrderController;
use NotificationChannels\OneSignal\OneSignalChannel;

class NewPurchaseOrder extends BaseNotification
{

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $order;
    private $emailSetting;

    public function __construct(PurchaseOrder $order)
    {
        $this->order = $order;
        $this->company = $this->order->company;
        $this->emailSetting = PurchaseNotificationSetting::where('company_id', $this->company->id)->where('slug', 'new-purchase-order')->first();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['database'];

        if ($this->emailSetting->send_email == 'yes' && $notifiable->email != '') {
            array_push($via, 'mail');
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage|void
     */
    public function toMail($notifiable)
    {

        if ($this->order->vendor) {
            // For Sending pdf to email
            $PurchaseOrderController = new PurchaseOrderController();

            if ($pdfOption = $PurchaseOrderController->domPdfObjectForDownload($this->order->id)) {
                $pdf = $pdfOption['pdf'];
                $filename = $pdfOption['fileName'];

                $content = __('purchase::email.purchaseOrder.text') .' '. $this->order->vendor->currency->currency_symbol .''. $this->order->total;

                $newOrder = parent::build();
                $newOrder->subject(__('purchase::email.purchaseOrder.subject') . ' - ' . config('app.name') . '.')
                    ->markdown('mail.email', [
                        'content' => $content,
                        'themeColor' => $this->company->header_color,
                        'notifiableName' => $notifiable->name
                    ]);
                $newOrder->attachData($pdf->output(), $filename . '.pdf');

                return $newOrder;
            }
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    //phpcs:ignore
    public function toArray($notifiable)
    {
        return [
            'id' => $this->order->id,
            'purchase_order_number' => $this->order->purchase_order_number
        ];
    }

}
