<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Entities\RecruitJobHistory;
use Modules\Recruit\Events\NewJobEvent;
use Modules\Recruit\Events\RecruitJobAlertUpdateEvent;
use Modules\Recruit\Events\UpdateJobEvent;

class JobsObserver
{
    public function saving(RecruitJob $event)
    {
        if (! isRunningInConsoleOrSeeding() && user()) {
            $event->last_updated_by = user()->id;
        }
    }

    public function creating(RecruitJob $event)
    {
        if (! isRunningInConsoleOrSeeding() && user()) {
            $event->added_by = user()->id;
        }

        if (company()) {
            $event->company_id = company()->id;
        }
    }

    public function created(RecruitJob $event)
    {
        if (! isRunningInConsoleOrSeeding()) {
            if (\user()) {
                $this->logRecruitJobsActivity($event->id, user()->id, 'createJob', null, null, null);
            }

            event(new NewJobEvent($event));
        }
    }

    public function updating(RecruitJob $event)
    {
        if ($event->isDirty('recruit_job_category_id') || $event->isDirty('recruit_job_type_id') || $event->isDirty('recruit_work_experience_id')) {
            event(new RecruitJobAlertUpdateEvent($event));
        }
    }

    public function updated(RecruitJob $event)
    {
        if (! isRunningInConsoleOrSeeding()) {
            if (\user()) {
                $this->logRecruitJobsActivity($event->id, user()->id, 'updateJob', null, null, null);
            }

            event(new UpdateJobEvent($event));
        }
    }

    public function logRecruitJobsActivity($jobID, $userID, $text, $jobapplicationID, $interviewID, $letterID)
    {
        $activity = new RecruitJobHistory;

        if (! is_null($jobID)) {
            $activity->recruit_job_id = $jobID;
        }

        if (! is_null($jobapplicationID)) {
            $activity->recruit_job_application_id = $jobapplicationID;
        }

        if (! is_null($interviewID)) {
            $activity->recruit_interview_schedule_id = $interviewID;
        }

        if (! is_null($letterID)) {
            $activity->recruit_job_offer_letter_id = $letterID;
        }

        $activity->user_id = $userID;
        $activity->details = $text;
        $activity->save();
    }
}
