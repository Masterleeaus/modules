<?php

namespace Modules\Recruit\Listeners;

use Modules\Recruit\Events\UpdateInterviewScheduleEvent;
use Modules\Recruit\Notifications\RecruiterUpdateInterviewSchedule;
use Modules\Recruit\Notifications\UpdateScheduleInterview;
use Notification;

class UpdateInterviewScheduleListener
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
    public function handle(UpdateInterviewScheduleEvent $interview)
    {
        $recruiter = $interview->interview->jobApplication->job->recruiter;
        Notification::send($recruiter, new RecruiterUpdateInterviewSchedule($interview->interview));

        Notification::send($interview->employee, new UpdateScheduleInterview($interview->interview));
    }

}
