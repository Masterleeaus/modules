<?php

namespace Modules\Recruit\Listeners;

use Notification;
use App\Models\User;
use Modules\Recruit\Events\NewJobEvent;
use Modules\Recruit\Notifications\JobRecruiter;
use Modules\Recruit\Notifications\AdminNewJob;

class NewJobListener
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
    public function handle(NewJobEvent $event)
    {
        $users = User::allAdmins($event->job->company->id)->pluck('id')->toArray();
        $id = $event->job->recruiter_id;

        Notification::send($event->job->recruiter, new JobRecruiter($event->job));

        if (!in_array($id, $users)) {
            Notification::send(User::allAdmins($event->job->company->id), new AdminNewJob($event->job));
        }
    }

}
