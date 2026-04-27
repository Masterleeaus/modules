<?php

namespace Modules\Recruit\Listeners;

use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Entities\RecruitJobAlert;
use Modules\Recruit\Entities\RecruitSetting;
use Modules\Recruit\Events\RecruitJobAlertEvent;
use Modules\Recruit\Notifications\RecruitJobAlertNotification;
use Notification;

class RecruitJobAlertListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(RecruitJobAlertEvent $event)
    {
        $setting = RecruitSetting::first();

        if ($setting->job_alert_status !== 'yes') {
            return;
        }

        $job = RecruitJob::with('address')->findOrFail($event->job->recruit_job_id);

        RecruitJobAlert::where('status', 'active')
            ->where('recruit_work_experience_id', $job->recruit_work_experience_id)
            ->where('recruit_job_type_id', $job->recruit_job_type_id)
            ->where('recruit_job_category_id', $job->recruit_job_category_id)
            ->get()
            ->each(function ($alert) use ($job) {
                $job->address->contains('id', $alert->location_id)
                    ? Notification::send($alert, new RecruitJobAlertNotification($job))
                    : null;
            });
    }

}
