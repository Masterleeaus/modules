<?php

namespace Modules\Recruit\Notifications;

use App\Notifications\BaseNotification;
use Modules\Recruit\Entities\RecruitEmailNotificationSetting;
use Modules\Recruit\Entities\RecruitJobOfferLetter;

class AdminNewOfferLetter extends BaseNotification
{

    private $offer;
    private $emailSetting;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(RecruitJobOfferLetter $offer)
    {
        $this->offer = $offer;
        $this->company = $this->offer->job->company;
        $this->emailSetting = RecruitEmailNotificationSetting::where('company_id', $this->company->id)->where('slug', 'new-offer-letteradded-by-admin')->first();
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

        if ($this->emailSetting->send_email == 'yes'  && $notifiable->email_notifications && $notifiable->email != null) {
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
        $url = route('job-offer-letter.show', $this->offer->id);
        $url = getDomainSpecificUrl($url, $this->company);

        $content = __($this->offer->jobApplication->full_name) . ' (' . ($this->offer->jobApplication->email ? $this->offer->jobApplication->email : '') . ') - ' . __('recruit::modules.offerLetter.text') . ' ' . ucwords($this->offer->job->title);

        return parent::build()
            ->subject(__('recruit::modules.offerLetter.subject'))
            ->markdown('mail.email', [
                'url' => $url,
                'content' => $content,
                'themeColor' => $this->company->header_color,
                'actionText' => __('app.view') . ' ' . __('recruit::modules.job.offerletter'),
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
        $this->recruiter = RecruitJobOfferLetter::with('user')
            ->where('id', $this->offer->id)
            ->first();

        return [
            'user_id' => $notifiable->id,
            'offer_id' => $this->offer->id,
            'heading' => $this->offer->jobApplication->full_name,
        ];
    }

}
