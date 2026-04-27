<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitInterviewFile;
use Modules\Recruit\Entities\RecruitInterviewHistory;

class InterviewFileObserver
{
    public function saving(RecruitInterviewFile $file)
    {
        if (! isRunningInConsoleOrSeeding()) {
            $file->last_updated_by = user()->id;
        }
    }

    public function creating(RecruitInterviewFile $file)
    {
        if (! isRunningInConsoleOrSeeding()) {
            $file->added_by = $file->user_id;
        }
    }

    public function created(RecruitInterviewFile $event)
    {
        if (\user()) {
            $this->logRecruitInterviewActivity($event->id, user()->id, 'fileActivity', $event->recruit_interview_schedule_id);
        }
    }

    public function logRecruitInterviewActivity($fileID, $userID, $text, $interviewID)
    {
        $activity = new RecruitInterviewHistory;

        if (! is_null($fileID)) {
            $activity->recruit_interview_file_id = $fileID;
        }

        if (! is_null($interviewID)) {
            $activity->recruit_interview_schedule_id = $interviewID;
        }

        $activity->user_id = $userID;
        $activity->details = $text;
        $activity->save();
    }
}
