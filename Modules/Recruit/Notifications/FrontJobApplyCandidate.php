<?php

namespace Modules\Recruit\Notifications;

use App\Notifications\BaseNotification;
use Modules\Recruit\Entities\RecruitJobApplication;

class FrontJobApplyCandidate extends BaseNotification
{
    private $jobApplication;

    private $job;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(RecruitJobApplication $jobApplication, $job)
    {
        $this->jobApplication = $jobApplication;
        $this->job = $job;
        $this->company = $this->jobApplication->job->company;
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
        $emailContent = parent::build()
            ->subject(__('recruit::modules.jobApplication.jobApplication'))
            ->greeting(__('email.hello').' '.$notifiable->full_name.'!');

        $emailContent = $emailContent->line(__('recruit::messages.successApply'));

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
            'data' => $this->jobApplication->toArray(),
        ];
    }
}
