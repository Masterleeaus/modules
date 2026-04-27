<?php

namespace Modules\Purchase\Notifications;

use Illuminate\Bus\Queueable;
use App\Notifications\BaseNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Purchase\Entities\PurchaseNotificationSetting;
use Modules\Purchase\Http\Controllers\PurchaseBillController;

class NewPurchaseBill extends BaseNotification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $purchaseBill;
    private $emailSetting;

    public function __construct($purchaseBill)
    {
        $this->purchaseBill = $purchaseBill;
        $this->company = $this->purchaseBill->vendor->company;
        $this->emailSetting = PurchaseNotificationSetting::where('company_id', $this->company->id)->where('slug', 'new-purchase-bill')->first();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = [];

        if ($this->emailSetting->send_email == 'yes' && $notifiable->email != '') {
            array_push($via, 'mail');
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $billController = new PurchaseBillController();

        if ($pdfOption = $billController->domPdfObjectForDownload($this->purchaseBill->id)) {
            $pdf = $pdfOption['pdf'];
            $filename = $pdfOption['fileName'];
        }

        $emailContent = parent::build()
            ->subject(__('purchase::email.purchaseBill.NewBill.subject'))
            ->greeting(__('email.hello') . ' ' . $notifiable->primary_name . '!')
            ->line(__('purchase::email.purchaseBill.NewBill.text'). ' ' . $this->company->company_name . ' '. __('purchase::email.purchaseBill.NewBill.text2') .' '. $this->purchaseBill->vendor->currency->currency_symbol . ' ' . $this->purchaseBill->total)
            ->attachData($pdf->output(), $filename . '.pdf');

        return $emailContent->line(__('purchase::email.vendorPayment.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

}
