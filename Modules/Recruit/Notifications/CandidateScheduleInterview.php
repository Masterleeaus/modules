<?php

namespace Modules\Recruit\Notifications;

use App\Notifications\BaseNotification;
use Modules\Recruit\Entities\RecruitInterviewSchedule;

class CandidateScheduleInterview extends BaseNotification
{

    private $interview;
    private $comments;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct(RecruitInterviewSchedule $interview, $comments = null)
    {
        $this->interview = $interview;
        $this->comments = $comments;
        $this->company = $this->interview->jobApplication->job->company;
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
        $emailContent = parent::build()
            ->subject(__('recruit::modules.email.subject'))
            ->greeting(__('email.hello') . ' ' . $notifiable->full_name . '!')
            ->line(__('recruit::modules.email.your') . ' ' . __('recruit::modules.email.text') . ' - ' . $this->interview->jobApplication->job->title)
            ->line(__('recruit::modules.email.on') . ' - ' . $this->interview->schedule_date->setTimeZone($this->company->timezone)->format($this->company->date_format. ' - ' . $this->company->time_format));
            

        if ($this->interview->interview_type == 'in person') {
            $emailContent = $emailContent->line(__('recruit::modules.interviewSchedule.interviewType') . ' - ' . __('recruit::app.interviewSchedule.inPerson'));
        }
        elseif ($this->interview->interview_type == 'video') {
            if ($this->interview->video_type == 'zoom') {
                $emailContent = $emailContent->line(__('recruit::modules.interviewSchedule.meetingPassword') . ' - ' . $this->interview->meeting->password);
                $emailContent = $emailContent->action(__('recruit::modules.interviewSchedule.joinUrl'), url($this->interview->meeting->join_link));
            }
            else {
                $emailContent = $emailContent->line(__('recruit::modules.interviewSchedule.interviewType') . ' - ' . $this->interview->other_link);
            }
        }
        elseif ($this->interview->interview_type == 'phone') {
            $emailContent = $emailContent->line(__('recruit::modules.interviewSchedule.interviewType') . ' - ' . ($this->interview->phone));
        }

        $emailContent = $emailContent->line($this->comments->candidate_comment ?? null);

        return $emailContent->line(__('recruit::modules.email.thankyouNote'));
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
            'data' => $this->interview->toArray()
        ];
    }

}
