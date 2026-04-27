<?php

namespace Modules\Recruit\Notifications;

use App\Models\Company;
use App\Notifications\BaseNotification;
use App\Scopes\CompanyScope;
use Carbon\Carbon;

class SendOfferLetterReminder extends BaseNotification
{
    private $event;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($event)
    {
        $this->event = $event;
        $company_id = $event->event->company_id;
        $this->company = Company::withoutGlobalScope(CompanyScope::class)->where('id', $company_id)->firstOrFail();
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
        $expiryDate = Carbon::createFromFormat('Y-m-d', $this->event->event->job_expire, $this->company->timezone)->format('d-m-Y');

        return parent::build()
            ->subject(__('recruit::modules.offerLetter.reminderSubject'))
            ->greeting(__('email.hello') . ' ' . $notifiable->full_name . '!')
            ->line(__('recruit::modules.offerLetter.reminderText') . ' ' . $expiryDate . '. ' . __('recruit::modules.offerLetter.reminderTextline'))
            ->line(__('recruit::modules.email.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray()
    {
        return [
            'data' => $this->event->toArray()
        ];
    }

}
