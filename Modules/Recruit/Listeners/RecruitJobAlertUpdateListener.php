<?php

namespace Modules\Recruit\Listeners;

use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Entities\RecruitJobAlert;
use Modules\Recruit\Entities\RecruitSetting;
use Modules\Recruit\Events\RecruitJobAlertUpdateEvent;
use Modules\Recruit\Notifications\RecruitJobAlertNotification;
use Notification;

class RecruitJobAlertUpdateListener
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
     * @param object $event
     * @return void
     */
    public function handle(RecruitJobAlertUpdateEvent $event)
    {
        $alertCheck = RecruitSetting::first();
        $jobAlerts = RecruitJobAlert::where('status', '=', 'active')->get();
        $jobLocations = RecruitJob::with('address')->where('id', $event->job->id)->first();

        if ($alertCheck->job_alert_status !== 'yes') {
            return;
        }

        foreach ($jobAlerts as $jobAlert) {
            if (!is_null($jobAlert) && $jobAlert->recruit_work_experience_id == $event->job->recruit_work_experience_id && $jobAlert->recruit_job_type_id == $event->job->recruit_job_type_id && $jobAlert->recruit_job_category_id == $event->job->recruit_job_category_id) {

                foreach ($jobLocations->address as $jobLocation) {
                    if ($jobLocation->id == $jobAlert->location_id) {
                        Notification::send($jobAlert, new RecruitJobAlertNotification($jobLocations));
                    }
                }
            }
        }
    }

}
