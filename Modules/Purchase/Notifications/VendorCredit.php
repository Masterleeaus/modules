<?php

namespace Modules\Purchase\Notifications;

use App\Models\Company;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Purchase\Entities\PurchaseNotificationSetting;
use Modules\Purchase\Entities\PurchaseVendorCredit;

class VendorCredit extends BaseNotification
{


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $vendorCredit;
    private $emailSetting;

    public function __construct($vendorCredit)
    {
        $this->vendorCredit = $vendorCredit;
        $this->company = Company::findOrFail($this->vendorCredit->company_id);
        $this->emailSetting = PurchaseNotificationSetting::where('company_id', $this->company->id)->where('slug', 'vendor-credit')->first();
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
     * @return MailMessage
     */

    public function toMail($notifiable): MailMessage
    {
        $content = __('purchase::email.vendorCredit.content') .' '.' ' . $this->vendorCredit->total . ' '.$this->company->currency->currency_code.' ' . __('app.amount');
        return parent::build()
            ->subject(__('purchase::email.vendorCredit.subject') . ' - ' . config('app.name') . '.')
            ->greeting(__('email.hello') . ' ' . $notifiable->primary_name . ',')
            ->markdown('mail.email', [
                'content' => $content,
                'notifiableName' => $notifiable->primary_name,
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

    }

}
