<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitInterviewHistory;
use Modules\Recruit\Entities\RecruitInterviewSchedule;
use Modules\Recruit\Entities\RecruitJobHistory;

class InterviewScheduleObserver
{
    public function creating(RecruitInterviewSchedule $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }

    public function created(RecruitInterviewSchedule $event)
    {
        if (\user()) {
            $jobID = $event->jobApplication->recruit_job_id;
            $this->logRecruitInterviewActivity($jobID, user()->id, 'createInterview', $event->id);
        }
    }

    public function updated(RecruitInterviewSchedule $event)
    {
        if (\user()) {
            $jobID = $event->jobApplication->recruit_job_id;
            $this->logRecruitInterviewActivity($jobID, user()->id, 'updateInterview', $event->id);
        }
    }

    public function logRecruitInterviewActivity($jobId, $userID, $text, $interviewID = null)
    {
        $activity = new RecruitInterviewHistory;

        if (! is_null($interviewID)) {
            $activity->recruit_interview_schedule_id = $interviewID;
        }

        $activity->user_id = $userID;
        $activity->details = $text;
        $activity->save();

        $activity = new RecruitJobHistory;

        if (! is_null($interviewID)) {
            $activity->recruit_interview_schedule_id = $interviewID;
        }

        $activity->recruit_job_id = $jobId;
        $activity->user_id = $userID;
        $activity->details = $text;
        $activity->save();
    }
}
