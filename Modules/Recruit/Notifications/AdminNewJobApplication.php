<?php

namespace Modules\Recruit\Notifications;

use App\Notifications\BaseNotification;
use Modules\Recruit\Entities\RecruitEmailNotificationSetting;
use Modules\Recruit\Entities\RecruitJobApplication;

class AdminNewJobApplication extends BaseNotification
{

    private $jobApplication;
    private $emailSetting;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(RecruitJobApplication $jobApplication)
    {
        $this->jobApplication = $jobApplication;
        $this->company = $this->jobApplication->company;
        $this->emailSetting = RecruitEmailNotificationSetting::where('company_id', $this->company->id)->where('slug', 'new-job-applicationadded-by-admin')->first();
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

        $email = $this->jobApplication->email ?? __('recruit::modules.front.email');

        $content = __(':full_name (:email) :text - :job_title', [
            'full_name' => $this->jobApplication->full_name,
            'email' => $email,
            'text' => __('recruit::modules.newJobApplication.text'),
            'job_title' => $this->jobApplication->job->title,
              ]);

        return parent::build()
            ->subject(__('recruit::modules.adminMail.newJobApplicationSubject'))
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
        $this->recruiter = RecruitJobApplication::with('user')->where('id', $this->jobApplication->id)->first();

        return [
            'user_id' => $notifiable->id,
            'jobApp_id' => $this->jobApplication->id,
            'heading' => $this->jobApplication->full_name,
        ];
    }

}
