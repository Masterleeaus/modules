<?php

namespace Modules\Recruit\Listeners;

use Modules\Recruit\Events\HostInterviewEvent;
use Modules\Recruit\Notifications\HostInterview;
use Modules\Zoom\Entities\ZoomMeeting;
use Notification;

class HostInterviewListener
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
    public function handle(HostInterviewEvent $interviewSchedule)
    {
        if (module_enabled('Zoom')) {
            $meetingID = $interviewSchedule->interviewSchedule->meeting_id;
            $host = ZoomMeeting::with('host')->where('id', $meetingID)->first()->host;
        }

        Notification::send($host, new HostInterview($interviewSchedule->interviewSchedule));
    }

}
