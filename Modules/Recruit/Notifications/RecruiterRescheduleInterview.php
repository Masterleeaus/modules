<?php

namespace Modules\Recruit\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Recruit\Entities\RecruitEmailNotificationSetting;
use Modules\Recruit\Entities\RecruitInterviewSchedule;

class RecruiterRescheduleInterview extends BaseNotification
{

    private $interview;
    private $emailSetting;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(RecruitInterviewSchedule $interview)
    {
        $this->interview = $interview;
        $this->company = $this->interview->jobApplication->job->company;
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
        $url = route('interview-schedule.show', $this->interview->id);
        $url = getDomainSpecificUrl($url, $this->company);

        $content = __($this->interview->jobApplication->full_name) . ' ' . __('recruit::modules.email.rescheduleText') . ' - ' . ucwords($this->interview->jobApplication->job->title) . '<br>' . ' ' .
        __('recruit::modules.email.on') . ' - ' . $this->interview->schedule_date->setTimeZone($this->company->timezone)->format($this->company->date_format. ' - ' . $this->company->time_format) . '<br>';

        if ($this->interview->interview_type == 'in person') {
            $content .= __('recruit::modules.interviewSchedule.interviewType') . ' - ' . __('recruit::app.interviewSchedule.inPerson');
        }
        elseif ($this->interview->interview_type == 'video') {

            if ($this->interview->video_type == 'zoom') {
                $content .= __('recruit::modules.interviewSchedule.interviewType') . ' - ' . __('recruit::app.interviewSchedule.zoom');
            }
            else {
                $content .= __('recruit::modules.interviewSchedule.interviewType') . ' - ' . $this->interview->other_link;
            }
        }
        elseif ($this->interview->interview_type == 'phone') {
            $content .= __('recruit::modules.interviewSchedule.interviewType') . ' - ' . $this->interview->phone;
        }

        return parent::build()
            ->subject(__('recruit::modules.adminMail.rescheduleSubject'))
            ->markdown('mail.email', [
                'url' => $url,
                'content' => $content,
                'themeColor' => $this->company->header_color,
                'actionText' => __('app.view') . ' ' . __('recruit::modules.interviewSchedule.interview'),
                'notifiableName' => $notifiable->name
            ]);
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
            'user_id' => $this->interview->jobApplication->job->recruiter_id,
            'interview_id' => $this->interview->id,
            'heading' => $this->interview->jobApplication->full_name
        ];
    }

}
