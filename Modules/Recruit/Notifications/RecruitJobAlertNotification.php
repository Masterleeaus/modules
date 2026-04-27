<?php

namespace Modules\Recruit\Notifications;

use App\Notifications\BaseNotification;
use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Entities\RecruitJobAlert;
use Modules\Recruit\Entities\RecruitJobCategory;

class RecruitJobAlertNotification extends BaseNotification
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $job;

    public function __construct(RecruitJob $job)
    {
        $this->job = $job;
        $this->company = $this->job->company;
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
        $jobLocation = RecruitJob::with('address')->where('id', $this->job->id)->first();
        $alertHash = RecruitJobAlert::where('email', $notifiable->email)->first();

        foreach ($jobLocation->address as $address) {
            if ($address->id == $notifiable->location_id) {
                $it = $address->id;
                $locationName = $address->location;
            }
        }

        $applyUrl = route('job_apply', [$this->job->slug, $it, $this->job->company->hash]);
        $url = route('front.job_alert_unsubscribe', [$this->job->company->hash, $alertHash->hashname]);
        $applyUrl = getDomainSpecificUrl($applyUrl, $this->company);
        $url = getDomainSpecificUrl($url, $this->company);

        $category = RecruitJobCategory::where('id', $this->job->recruit_job_category_id)->select('category_name')->first();
        $endDate = $this->job->end_date ? $this->job->end_date->format('M d, Y h:i a') : 'null';

        $content = '<p>'.__('recruit::modules.newJob.alertMsg').' '.ucwords($category->category_name).' </p> <p>'.__('recruit::modules.job.job').' - '.$this->job->title.'
        </p> <p>'.__('recruit::modules.job.job').' '.__('recruit::modules.job.location').' - '.$locationName.' </p> <p>'.__('recruit::modules.newJob.lastDate').' - '.$endDate.' </p>';

        $emailContent = parent::build()
            ->subject(__('recruit::modules.setting.jobAlert'));

        $emailContent->markdown('recruit::mail.recruit-job-alert.alert', ['applyUrl' => $applyUrl, 'url' => $url, 'content' => $content]);

        return $emailContent->line(__('recruit::modules.email.thankyouNote'));
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
            'data' => $this->job->toArray(),
        ];
    }
}
