<?php

namespace Modules\Recruit\Listeners;

use Modules\Recruit\Events\CandidateInterviewRescheduleEvent;
use Modules\Recruit\Notifications\CandidateRescheduleInterview;
use Notification;

class CandidateInterviewRescheduleListener
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(CandidateInterviewRescheduleEvent $interview)
    {
        if ($interview->interview->jobApplication->email != null) {
            Notification::send($interview->interview->jobApplication, new CandidateRescheduleInterview($interview->interview, $interview->candidateComment));
        }
    }

}
