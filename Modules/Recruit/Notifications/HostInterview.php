<?php

namespace Modules\Recruit\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Recruit\Entities\RecruitInterviewSchedule;

class HostInterview extends BaseNotification
{

    private $interviewSchedule;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct(RecruitInterviewSchedule $interviewSchedule)
    {
        $this->interviewSchedule = $interviewSchedule;
        $this->company = $this->interviewSchedule->jobApplication->job->company;
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
        $url = route('interview-schedule.show', $this->interviewSchedule->id);
        $url = getDomainSpecificUrl($url, $this->company);

        $emailContent = parent::build()
            ->subject(__('recruit::modules.email.subject'))
            ->greeting(__('email.hello') . ' ' . $notifiable->name . '!')
            ->line(__('recruit::modules.email.hostText') . ' ' . __($this->interviewSchedule->jobApplication->full_name))
            ->line(__('recruit::modules.email.for') . ' - ' . $this->interviewSchedule->jobApplication->job->title . ' ' . __('recruit::modules.email.hasBeenSchedule'))
            ->line(__('recruit::modules.email.atDate') . ' ' . $this->interviewSchedule->schedule_date->format('M d, Y h:i a'))
            ->action(__('app.view') . ' ' . __('recruit::modules.interviewSchedule.interview'), $url);

        if ($notifiable->id == $this->interviewSchedule->meeting->created_by) {
            $emailContent = $emailContent->line(__('recruit::modules.interviewSchedule.meetingPassword') . ' - ' . $this->interviewSchedule->meeting->password);
            $emailContent = $emailContent->action(__('recruit::modules.interviewSchedule.startUrl'), url($this->interviewSchedule->meeting->start_link));
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
            'interview_id' => $this->interviewSchedule->id,
            'heading' => $this->interviewSchedule->jobApplication->job->title
        ];
    }

}
