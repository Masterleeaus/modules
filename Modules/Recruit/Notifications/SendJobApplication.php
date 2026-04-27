<?php

namespace Modules\Recruit\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Recruit\Entities\RecruitEmailNotificationSetting;
use Modules\Recruit\Entities\RecruitJobApplication;

class SendJobApplication extends BaseNotification
{
    use Queueable;
    private $applicant;
    // private $user;
    private $emailSetting;

    /**
     * Create a new notification instance.
     */
    public function __construct(RecruitJobApplication $jobApplication)
    {

        $this->applicant = $jobApplication;
        $this->company = $this->applicant->company;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        $via = ['database'];
        if ($notifiable->email) {
            array_push($via, 'mail');
        }

        return $via;
       

    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        
        $url = route('job-applications.show', $this->applicant->id);
        $url = getDomainSpecificUrl($url, $this->company);
       
        $email = $this->applicant->email ?? __('recruit::modules.front.email');

         $content = __(':full_name (:email) :text - :job_title', [
        'full_name' => $this->applicant->full_name,
        'email' => $email,
        'text' => __('recruit::modules.newJobApplication.text'),
        'job_title' => $this->applicant->job->title,
          ]);

        return parent::build()
            ->subject(__('recruit::modules.newJobApplication.subject'))
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
     */
    public function toArray($notifiable): array
    {
        return [
            'user_id' => $this->applicant->job->recruiter_id,
            'jobApp_id' => $this->applicant->id,
            'heading' => $this->applicant->full_name
        ];
    }

}
