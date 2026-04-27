<?php

namespace Modules\Recruit\Notifications;

use App\Notifications\BaseNotification;
use Modules\Recruit\Entities\RecruitCandidateFollowUp;

class CandidateFollowUpReminder extends BaseNotification
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */

    private $followup;

    public function __construct(RecruitCandidateFollowUp $followup)
    {
        $this->followup = $followup;
        $this->company = $followup->application->company;
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

        if ($notifiable->email != '') {
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
        $url = route('job-applications.show', $this->followup->application->id) . '?view=follow-up';

        $url = getDomainSpecificUrl($url, $this->company);

        $followUpDate = (!is_null($this->followup->next_follow_up_date)) ? $this->followup->next_follow_up_date->format($this->company->date_format) : null;

        $content = __('email.followUpReminder.nextFollowUpDate') . ' :- ' . $followUpDate . '<br>' . $this->followup->remark;

        return parent::build()
            ->subject(__('recruit::modules.followUpReminder.subject') . ' #' . $this->followup->id . ' - ' . config('app.name') . '.')
            ->markdown('mail.email', [
                'url' => $url,
                'content' => $content,
                'themeColor' => $this->company->header_color,
                'actionText' => __('email.followUpReminder.action'),
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
            'id' => $this->followup->id,
            'user_id' => $notifiable->id,
            'job_application_id' => $this->followup->application->id,
            'heading' => $this->followup->application->full_name
        ];
    }

}
