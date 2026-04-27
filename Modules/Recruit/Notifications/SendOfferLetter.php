<?php

namespace Modules\Recruit\Notifications;

use App\Notifications\BaseNotification;
use Modules\Recruit\Entities\RecruitJobOfferLetter;

class SendOfferLetter extends BaseNotification
{
    private $offer;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(RecruitJobOfferLetter $offer)
    {
        $this->offer = $offer;
        $this->company = $this->offer->job->company;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
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
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('front.jobOffer', $this->offer->hash, $this->company);
        $url = getDomainSpecificUrl($url, $this->company);

        $emailContent = parent::build()
            ->subject(__('recruit::modules.email.jobOffer'))
            ->greeting(__('email.hello').' '.$notifiable->full_name.'!')
            ->action(__('recruit::app.jobOffer.viewoffer'), $url);

        return $emailContent->line(__('recruit::modules.email.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray()
    {
        return [
            'data' => $this->offer->toArray(),
        ];
    }
}
