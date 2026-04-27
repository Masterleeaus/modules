<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitInterviewEvaluation;
use Modules\Recruit\Entities\RecruitInterviewHistory;

class EvaluationObserver
{
    public function created(RecruitInterviewEvaluation $event)
    {
        if (\user()) {
            $scheduleId = $event->interview_schedule_id;
            $this->logRecruitEvaluationActivity(user()->id, 'createEvaluation', $scheduleId);
        }
    }

    public function updated(RecruitInterviewEvaluation $event)
    {
        if (\user()) {
            $scheduleId = $event->interview_schedule_id;
            $this->logRecruitEvaluationActivity(user()->id, 'updateEvaluation', $scheduleId);
        }
    }

    public function logRecruitEvaluationActivity($userID, $text, $interviewID)
    {
        $activity = new RecruitInterviewHistory;

        if (! is_null($interviewID)) {
            $activity->recruit_interview_schedule_id = $interviewID;
        }

        $activity->user_id = $userID;
        $activity->details = $text;
        $activity->save();
    }
}
