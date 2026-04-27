<?php

namespace Modules\Recruit\Listeners;

use App\Models\User;
use Notification;
use Modules\Recruit\Events\InterviewScheduleEvent;
use Modules\Recruit\Notifications\AdminNewInterviewSchedule;
use Modules\Recruit\Notifications\ScheduleInterview;
use Modules\Recruit\Notifications\RecruiterInterviewSchedule;

class InterviewScheduleListener
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
    public function handle(InterviewScheduleEvent $interview)
    {
        $companyId = $interview->interview->jobApplication->job->company->id;
        $companyAdmins = User::allAdmins($companyId)->pluck('id')->toArray();
        $recruiterId = $interview->interview->jobApplication->job->recruiter_id;

        Notification::send(User::allAdmins($companyId), new AdminNewInterviewSchedule($interview->interview));

        if (!in_array($recruiterId, $companyAdmins)) {
            Notification::send($interview->interview->jobApplication->job->recruiter, new RecruiterInterviewSchedule($interview->interview));
        }
        Notification::send($interview->employee, new ScheduleInterview($interview->interview));
    }


}
