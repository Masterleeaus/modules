<?php

namespace Modules\Recruit\Listeners;

use Illuminate\Support\Facades\Notification;
use Modules\Recruit\Entities\RecruitApplicationStatus;
use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Events\UpdateJobApplicationEvent;
use Modules\Recruit\Notifications\RecruiterJobApplicationStatusChanged;
use Modules\Recruit\Notifications\UpdateJobApplication;

class UpdateJobApplicationListener
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
    public function handle(UpdateJobApplicationEvent $event)
    {
        $statusSlug = RecruitApplicationStatus::where('id', $event->jobApplication->status_id)->value('slug');

        if ($event->jobApplication->isDirty('status_id')) {
            $job = RecruitJob::findOrFail($event->jobApplication->job->id);
            $job->remaining_openings += $statusSlug === 'hired' ? -1 : 1;
            $job->save();

            Notification::send($event->jobApplication->job->recruiter, new RecruiterJobApplicationStatusChanged($event->jobApplication));
        }
        else {
            Notification::send($event->jobApplication->job->recruiter, new UpdateJobApplication($event->jobApplication));
        }

    }

}
