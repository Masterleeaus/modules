<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Entities\RecruitJobHistory;
use Modules\Recruit\Events\NewJobApplicationEvent;
use Modules\Recruit\Entities\RecruitJobApplication;
use Modules\Recruit\Events\UpdateJobApplicationEvent;

class JobApplicationsObserver
{

    public function saving(RecruitJobApplication $event)
    {
        if (! isRunningInConsoleOrSeeding() && user()) {
            $event->last_updated_by = user()->id;
        }
    }

    public function creating(RecruitJobApplication $event)
    {
        if (! isRunningInConsoleOrSeeding() && user()) {
            $event->added_by = user()->id;
        }

        if (company()) {
            $event->company_id = company()->id;
        }
    }

    public function created(RecruitJobApplication $event)
    {
        if (! isRunningInConsoleOrSeeding() && request()->type != 'import') {
            if (\user()) {
                $this->logRecruitJobsActivity($event->recruit_job_id, user()->id, 'createJobapplication', $event->id, null, $event->recruit_application_status_id);
            }
            $job=RecruitJob::where('id', $event->recruit_job_id)->first();

            event(new NewJobApplicationEvent($event, $job));
        }
    }

    public function updating(RecruitJobApplication $event)
    {
        if (! isRunningInConsoleOrSeeding() && \user()) {
            $this->logRecruitJobsActivity($event->recruit_job_id, user()->id, 'updateJobapplication', $event->id, null);

            if ($event->isDirty('recruit_application_status_id')) {
                $this->logRecruitJobsActivity($event->recruit_job_id, user()->id, 'updateJobapplicationStatus', $event->id, null, $event->recruit_application_status_id);
            }
        }

    }

    public function updated(RecruitJobApplication $event)
    {

        if (! isRunningInConsoleOrSeeding() && request()->type != 'import') {
            event(new UpdateJobApplicationEvent($event));
        }
    }

    public function logRecruitJobsActivity($jobID, $userID, $text, $jobapplicationID, $interviewID, $statusID = null)
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

        if (! is_null($statusID)) {
            $activity->recruit_job_application_status_id = $statusID;
        }

        $activity->user_id = $userID;
        $activity->details = $text;
        try
        {
            $activity->save();
        }
        catch(\Exception $e)
        {
            dd($e->getMessage());
        }
    }

}
