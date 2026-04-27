<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitJobFile;
use Modules\Recruit\Entities\RecruitJobHistory;

class JobFileObserver
{
    public function saving(RecruitJobFile $file)
    {
        if (! isRunningInConsoleOrSeeding()) {
            $file->last_updated_by = user()->id;
        }
    }

    public function creating(RecruitJobFile $file)
    {
        if (! isRunningInConsoleOrSeeding()) {
            $file->added_by = $file->user_id;
        }
    }

    public function created(RecruitJobFile $event)
    {
        if (\user()) {
            $this->logRecruitJobsActivity($event->id, user()->id, 'fileActivity', null);
        }
    }

    public function logRecruitJobsActivity($fileID, $userID, $text, $interviewID)
    {
        $activity = new RecruitJobHistory;

        if (! is_null($fileID)) {
            $activity->file_id = $fileID;
        }

        if (! is_null($interviewID)) {
            $activity->recruit_interview_schedule_id = $interviewID;
        }

        $activity->user_id = $userID;
        $activity->details = $text;
        $activity->save();
    }
}
