<?php

namespace Modules\Recruit\Notifications;

use App\Notifications\BaseNotification;

use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Entities\RecruitJobApplication;

class OfferLetterAccept extends BaseNotification
{

    private $jobApplication;
    private $job;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct(RecruitJob $job, RecruitJobApplication $jobApplication)
    {
        $this->job = $job;
        $this->jobApplication = $jobApplication;
        $this->company = $this->job->company;
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
        return parent::build()
            ->subject(__('recruit::modules.offerAccept.subject'))
            ->greeting(__('email.hello') . ' ' . $notifiable->full_name . '!')
            ->line(__('recruit::modules.offerAccept.text') . ' - ' . ucwords($this->job->title))
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
            'data' => $this->jobApplication->toArray()
        ];
    }

}
