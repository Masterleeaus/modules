<?php

namespace Modules\Recruit\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Recruit\Entities\RecruitInterviewSchedule;

class ScheduleInterview extends BaseNotification
{

    private $interview;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct(RecruitInterviewSchedule $interview)
    {
        $this->interview = $interview;
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
        $via = ['database'];

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
        $url = route('interview-schedule.show', $this->interview->id);
        $url = getDomainSpecificUrl($url, $this->company);

        $emailContent = parent::build()
            ->subject(__('recruit::modules.email.subject'))
            ->greeting(__('email.hello') . ' ' . $notifiable->name . '!')
            ->line(__($this->interview->jobApplication->full_name) . ' ' . __('recruit::modules.email.text') . ' - ' . ucwords($this->interview->jobApplication->job->title))
            ->line(__('recruit::modules.email.on') . ' - ' . $this->interview->schedule_date->setTimeZone($this->company->timezone)->format($this->company->date_format. ' - ' . $this->company->time_format))
            
            ->action(__('app.view') . ' ' . __('recruit::modules.interviewSchedule.interview'), $url);

        if ($this->interview->interview_type == 'in person') {
            $emailContent = $emailContent->line(__('recruit::modules.interviewSchedule.interviewType') . ' - ' . __('recruit::app.interviewSchedule.inPerson'));
        }
        elseif ($this->interview->interview_type == 'video') {

            if ($this->interview->video_type == 'zoom') {

                if ($notifiable->id == $this->interview->meeting->created_by) {
                    $emailContent = $emailContent->line(__('recruit::modules.interviewSchedule.meetingPassword') . ' - ' . $this->interview->meeting->password);
                    $emailContent = $emailContent->action(__('recruit::modules.interviewSchedule.startUrl'), url($this->interview->meeting->start_link));
                }
                else {
                    $emailContent = $emailContent->line(__('recruit::modules.interviewSchedule.meetingPassword') . ' - ' . $this->interview->meeting->password);
                    $emailContent = $emailContent->action(__('recruit::modules.interviewSchedule.joinUrl'), url($this->interview->meeting->join_link));
                }

            }
            else {
                $emailContent = $emailContent->line(__('recruit::modules.interviewSchedule.interviewType') . ' - ' . ucwords($this->interview->other_link));
            }

        }
        elseif ($this->interview->interview_type == 'phone') {
            $emailContent = $emailContent->line(__('recruit::modules.interviewSchedule.interviewType') . ' - ' . ucwords($this->interview->phone));
        }

        return $emailContent->line(__('recruit::modules.email.thankyouNote'));
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
            'interview_id' => $this->interview->id,
            'heading' => $this->interview->jobApplication->full_name
        ];
    }

}
