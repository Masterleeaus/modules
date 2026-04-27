<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitInterviewEmployees;
use Modules\Recruit\Entities\RecruitInterviewHistory;

class InterviewEmployeeObserver
{
    public function created(RecruitInterviewEmployees $event)
    {
        if (\user()) {
            $this->logRecruitInterviewActivity(user()->id, 'createInterview', $event->interview_schedule_id, null);
        }
    }

    public function updated(RecruitInterviewEmployees $event)
    {
        if (\user()) {
            $this->logRecruitInterviewActivity(user()->id, 'updateInterview', $event->interview_schedule_id, null);
        }
    }

    public function logRecruitInterviewActivity($userID, $text, $interviewID)
    {
        $activity = new RecruitInterviewHistory;
        $activity->recruit_interview_schedule_id = $interviewID;
        $activity->user_id = $userID;
        $activity->details = $text;
        $activity->save();
    }
}
