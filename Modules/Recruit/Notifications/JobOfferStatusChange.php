<?php

namespace Modules\Recruit\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Modules\Recruit\Entities\RecruitEmailNotificationSetting;
use Modules\Recruit\Entities\RecruitJobOfferLetter;

class JobOfferStatusChange extends BaseNotification
{
    use Queueable;

    private $jobOffer;

    private $emailSetting;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(RecruitJobOfferLetter $jobOffer)
    {
        $this->jobOffer = $jobOffer;
        $this->company = $this->jobOffer->job->company;
        $this->emailSetting = RecruitEmailNotificationSetting::where('company_id', $this->company->id)->where('slug', 'notification-to-recruiter')->first();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['database'];

        if ($this->emailSetting->send_email == 'yes' && $notifiable->email_notifications && $notifiable->email != null) {
            array_push($via, 'mail');
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $status = ($this->jobOffer->status == 'accept') ? 'accepted' : 'declined';
        $emailContent = parent::build()
            ->subject(__('recruit::modules.email.jobOffer'))
            ->greeting(__('email.hello').' '.$notifiable->name.'!')
            ->line(__($this->jobOffer->jobApplication->full_name).' '.$status.' '.__('recruit::modules.message.jobOfferStatus'));

        return $emailContent->line(__('recruit::modules.email.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'user_id' => $notifiable->id,
            'offer_id' => $this->jobOffer->id,
            'heading' => $this->jobOffer->jobApplication->full_name,
        ];
    }
}
