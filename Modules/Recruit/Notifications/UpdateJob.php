<?php

namespace Modules\Recruit\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Recruit\Entities\RecruitEmailNotificationSetting;

class UpdateJob extends BaseNotification
{

    private $job;
    private $emailSetting;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct($job)
    {
        $this->job = $job;
        $this->company = $this->job->company;
        $this->emailSetting = RecruitEmailNotificationSetting::where('company_id', $this->company->id)->where('slug', 'notification-to-recruiter')->first();
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

        if ($this->emailSetting->send_email == 'yes' && $notifiable->email_notifications && $notifiable->email != null) {
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
        $url = route('jobs.show', $this->job->id);
        $url = getDomainSpecificUrl($url, $this->company);

        $content = $this->job->title . ' - ' . __('recruit::modules.updateJob.text2');

        return parent::build()
            ->subject(__('recruit::modules.updateJob.subject2'))
            ->markdown('mail.email', [
                'url' => $url,
                'content' => $content,
                'themeColor' => $this->company->header_color,
                'actionText' => __('app.view') . ' ' . __('recruit::app.jobOffer.job'),
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
            'user_id' => $notifiable->id,
            'job_id' => $this->job->id,
            'heading' => $this->job->title
        ];
    }

}
