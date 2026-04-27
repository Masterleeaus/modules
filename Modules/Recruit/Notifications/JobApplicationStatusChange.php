<?php

namespace Modules\Recruit\Notifications;

use App\Notifications\BaseNotification;
use Modules\Recruit\Entities\RecruitApplicationStatus;
use Modules\Recruit\Entities\RecruitApplicationStatusCategory;
use Modules\Recruit\Entities\RecruitJobApplication;

class JobApplicationStatusChange extends BaseNotification
{
    private $jobApplication;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(RecruitJobApplication $jobApplication)
    {
        $this->jobApplication = $jobApplication;
        $this->company = $this->jobApplication->company;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
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
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $recruitStatus = RecruitApplicationStatus::findOrFail($notifiable->recruit_application_status_id);
        $category = RecruitApplicationStatusCategory::where('company_id', $recruitStatus->company->id)->where('name', 'rejected')->first();

        $emailContent = parent::build()
            ->subject(__('recruit::messages.statusSubject'))
            ->greeting(__('email.hello').' '.$notifiable->full_name.'!')
            ->line(__('recruit::messages.greetingMessage'))
            ->line(__('recruit::messages.applicationStatus',[
                'status' => $recruitStatus->status,
                'job' => $this->jobApplication->job->title
            ]));
        if ($recruitStatus->recruit_application_status_category_id == $category->id) {
            $emailContent->line(__('recruit::messages.rejectedMessage'))
                ->line(__('recruit::messages.bestWishes'));
        }

        return $emailContent->line(__('recruit::messages.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray()
    {
        return [
            'data' => $this->jobApplication->toArray(),
        ];
    }

}
