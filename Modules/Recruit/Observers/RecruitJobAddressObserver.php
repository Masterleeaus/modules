<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitJobAddress;
use Modules\Recruit\Events\RecruitJobAlertEvent;

class RecruitJobAddressObserver
{
    public function created(RecruitJobAddress $event)
    {
        if (! isRunningInConsoleOrSeeding()) {
            event(new RecruitJobAlertEvent($event));
        }
    }
}
