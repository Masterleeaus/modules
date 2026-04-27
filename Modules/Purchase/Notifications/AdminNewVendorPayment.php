<?php

namespace Modules\Purchase\Notifications;

use App\Notifications\BaseNotification;
use Modules\Purchase\Entities\PurchaseNotificationSetting;

class AdminNewVendorPayment extends BaseNotification
{

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $payment;
    private $emailSetting;

    public function __construct($payment)
    {
        $this->payment = $payment;
        $this->company = $this->payment->vendor->company;
        $this->emailSetting = PurchaseNotificationSetting::where('company_id', $this->company->id)->where('slug', 'admin-new-vendor-payment')->first();
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
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('vendor-payments.show', $this->payment->id);
        $url = getDomainSpecificUrl($url, $this->company);

        $content = __('purchase::email.newPayment.text') . ' - ' . $this->payment->vendor->primary_name;

        return parent::build()
            ->subject(__('purchase::email.newPayment.subject'))
            ->markdown('mail.email', [
                'url' => $url,
                'content' => $content,
                'themeColor' => $this->company->header_color,
                'actionText' => __('purchase::email.newPayment.action'),
                'notifiableName' => $notifiable->name
            ]);
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
            'id' => $this->payment->id,
            'user_id' => $notifiable->id,
            'vendor_name' => $this->payment->vendor->primary_name
        ];
    }

}
