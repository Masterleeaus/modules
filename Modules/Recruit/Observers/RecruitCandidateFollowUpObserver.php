<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitCandidateFollowUp;

class RecruitCandidateFollowUpObserver
{
    public function saving(RecruitCandidateFollowUp $event)
    {
        if (! isRunningInConsoleOrSeeding() && user()) {
            $event->last_updated_by = user()->id;
        }
    }

    public function creating(RecruitCandidateFollowUp $event)
    {
        if (! isRunningInConsoleOrSeeding() && user()) {
            $event->added_by = user()->id;
        }
    }
}
