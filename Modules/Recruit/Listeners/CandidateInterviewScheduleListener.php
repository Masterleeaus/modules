<?php

namespace Modules\Recruit\Listeners;

use Modules\Recruit\Events\CandidateInterviewScheduleEvent;
use Modules\Recruit\Notifications\CandidateScheduleInterview;
use Notification;

class CandidateInterviewScheduleListener
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
    public function handle(CandidateInterviewScheduleEvent $interview)
    {
        if ($interview->interview->jobApplication->email != null) {
            Notification::send($interview->interview->jobApplication, new CandidateScheduleInterview($interview->interview, $interview->candidateComment));
        }
    }

}
