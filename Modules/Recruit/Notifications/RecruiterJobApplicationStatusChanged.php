<?php

namespace Modules\Recruit\Notifications;

use App\Notifications\BaseNotification;
use Modules\Recruit\Entities\RecruitEmailNotificationSetting;
use Modules\Recruit\Entities\RecruitJobApplication;

class RecruiterJobApplicationStatusChanged extends BaseNotification
{

    public $jobApplication;
    public $emailSetting;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(RecruitJobApplication $jobApplication)
    {
        $this->jobApplication = $jobApplication;
        $this->company = $this->jobApplication->company;
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
        $url = route('job-applications.show', $this->jobApplication->id);
        $url = getDomainSpecificUrl($url, $this->company);

        $content = __($this->jobApplication->full_name) . ' (' . $this->jobApplication->email ? $this->jobApplication->email : '' . ') - ' . __('recruit::modules.recruiterJobApplicationStatusChange.text') . ' ' . $this->jobApplication->applicationStatus->status;

        return parent::build()
            ->subject(__('recruit::modules.recruiterJobApplicationStatusChange.subject'))
            ->markdown('mail.email', [
                'url' => $url,
                'content' => $content,
                'themeColor' => $this->company->header_color,
                'actionText' => __('app.view') . ' ' . __('recruit::modules.jobApplication.jobApplication'),
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
            'job_id' => $this->jobApplication->id,
            'heading' => $this->jobApplication->full_name
        ];
    }

}
