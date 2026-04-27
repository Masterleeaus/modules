<?php

namespace Modules\Recruit\Listeners;

use Modules\Recruit\Events\InterviewRescheduleEvent;
use Modules\Recruit\Notifications\RecruiterRescheduleInterview;
use Modules\Recruit\Notifications\RescheduleInterview;
use Notification;

class InterviewRescheduleListener
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
    public function handle(InterviewRescheduleEvent $interview)
    {
        $recruiter = $interview->interview->jobApplication->job->recruiter;
        Notification::send($recruiter, new RecruiterRescheduleInterview($interview->interview));

        Notification::send($interview->employee, new RescheduleInterview($interview->interview));
    }

}
