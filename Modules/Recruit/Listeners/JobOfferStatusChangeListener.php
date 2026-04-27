<?php

namespace Modules\Recruit\Listeners;

use App\Models\User;
use Modules\Recruit\Events\JobOfferStatusChangeEvent;
use Modules\Recruit\Notifications\JobOfferStatusChange;
use Notification;

class JobOfferStatusChangeListener
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
    public function handle(JobOfferStatusChangeEvent $jobOffer)
    {
        $users = User::allAdmins($jobOffer->offer->job->company->id);
        $userIds = $users->pluck('id')->toArray();

        Notification::send($users, new JobofferStatusChange($jobOffer->offer));

        if (!in_array($jobOffer->offer->user->id, $userIds)) {
            Notification::send($jobOffer->offer->job->recruiter, new JobofferStatusChange($jobOffer->offer));
        }
    }

}
