<?php

namespace Modules\Recruit\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Entities\RecruitJobApplication;
use Modules\Recruit\Events\NewJobApplicationEvent;
use Modules\Recruit\Notifications\NewJobApplication;
use Modules\Recruit\Notifications\SendJobApplication;
use Modules\Recruit\Notifications\AdminNewJobApplication;
use Modules\Recruit\Notifications\FrontJobApplyCandidate;

class NewJobApplicationListener
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
    public function handle(NewJobApplicationEvent $event)
    {
        $jobApplication = $event->jobApplication;
        $job = $event->job;
        $companyId = $jobApplication->company->id;

        // Get company admins
        $companyAdmins = User::allAdmins($companyId);

        // Send admin notification
        $adminNotification = new AdminNewJobApplication($jobApplication);
        Notification::send($companyAdmins, $adminNotification);

        // Send notification to the recruiter
        $recruiter = $jobApplication->job->recruiter;

        if (isset($recruiter->email) && $recruiter->email) {
            Notification::send($recruiter, new NewJobApplication($jobApplication));
        }
        if ($event->jobApplication->send_email == 1) {
            // Send notification to the candidate if email exists
            Notification::send($jobApplication, new FrontJobApplyCandidate($jobApplication,$job));
        }
    }

}
