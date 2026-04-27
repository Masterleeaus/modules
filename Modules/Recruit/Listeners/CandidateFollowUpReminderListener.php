<?php

namespace Modules\Recruit\Listeners;

use Modules\Recruit\Events\CandidateFollowUpReminderEvent;
use Modules\Recruit\Notifications\CandidateFollowUpReminder;
use Notification;

class CandidateFollowUpReminderListener
{

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(CandidateFollowUpReminderEvent $event)
    {
        $notifyUser = $event->followup->application->job->recruiter;

        if ($notifyUser) {
            Notification::send($notifyUser, new CandidateFollowUpReminder($event->followup));
        }

    }

}
