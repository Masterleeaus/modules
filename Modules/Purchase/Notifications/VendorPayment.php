<?php

namespace Modules\Purchase\Notifications;

use App\Notifications\BaseNotification;

class VendorPayment extends BaseNotification
{

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $payment;

    public function __construct($payment)
    {
        $this->payment = $payment;
        $this->company = $this->payment->vendor->company;
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

        if ($notifiable->email) {
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
        $emailContent = parent::build()
            ->subject(__('purchase::email.vendorPayment.subject'))
            ->greeting(__('email.hello') . ' ' . $notifiable->primary_name . '!')
            ->line(__('purchase::email.vendorPayment.text'). ' ' . $this->company->company_name . ' '. __('purchase::email.vendorPayment.text2') .' '. $this->payment->vendor->currency->currency_symbol . ' ' . $this->payment->received_payment);

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
