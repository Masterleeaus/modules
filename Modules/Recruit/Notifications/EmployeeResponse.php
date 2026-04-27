<?php

namespace Modules\Recruit\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Recruit\Entities\RecruitInterviewEmployees;
use Modules\Recruit\Entities\RecruitJob;

class EmployeeResponse extends BaseNotification
{

    private $scheduleEmployee;
    private $type;
    private $userData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct(RecruitInterviewEmployees $scheduleEmployee, $type, $userData)
    {
        $this->scheduleEmployee = $scheduleEmployee;
        $this->type = $type;
        $this->userData = $userData;
        $this->company = $this->scheduleEmployee->user->company;
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
        $application = $this->scheduleEmployee->schedule->jobApplication;
        $job = RecruitJob::find($application->recruit_job_id);
        $emailContent = parent::build()
            ->subject(__('recruit::modules.email.subject'))
            ->greeting(__('email.hello') . ' ' . $notifiable->name . '!');

        if ($this->type == 'accepted') {
            $emailContent = $emailContent->line(__('recruit::messages.yourResponseAccept'));
            $emailContent = $emailContent->line(__('recruit::modules.interviewSchedule.interviewOn') . ' - ' . $this->scheduleEmployee->schedule->schedule_date->format('M d, Y h:i a'));

            $emailContent = $emailContent->line(__('recruit::modules.interviewSchedule.youHave') . ' ' . $this->scheduleEmployee->schedule->interview_type . ' ' . __('recruit::modules.interviewSchedule.forJob') . ' ' . $job->title . ' ' . __('recruit::modules.interviewSchedule.with') . ' ' . ucwords($this->scheduleEmployee->schedule->jobApplication->full_name));
        }
        else {
            $emailContent = $emailContent->line(__('recruit::messages.yourResponse'));
            $emailContent = $emailContent->line(__('recruit::modules.interviewSchedule.interviewWasOn') . ' - ' . $this->scheduleEmployee->schedule->schedule_date->format('M d, Y h:i a'));

            $emailContent = $emailContent->line(__('recruit::modules.interviewSchedule.youHad') . ' ' . $this->scheduleEmployee->schedule->interview_type . ' ' . __('recruit::modules.interviewSchedule.forJob') . ' ' . $job->title . ' ' . __('recruit::modules.interviewSchedule.with') . ' ' . ucwords($this->scheduleEmployee->schedule->jobApplication->full_name));
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
            'interview_id' => $this->scheduleEmployee->schedule->id,
            'heading' => $this->scheduleEmployee->schedule->jobApplication->full_name
        ];
    }

}
