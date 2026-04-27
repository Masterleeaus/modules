<?php

namespace Modules\Recruit\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Modules\Recruit\Events\UpdateOfferLetterEvent;
use Modules\Recruit\Notifications\RecruiterOfferLetter;
use Modules\Recruit\Notifications\UpdateOfferLetter;

class UpdateOfferLetterListener
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
    public function handle(UpdateOfferLetterEvent $event)
    {
        $jobOffer = $event->jobOffer;

        $recipientRecruiter = $jobOffer->job->recruiter;
        $notificationRecruiter = $jobOffer->isDirty('job_app_id') ? new RecruiterOfferLetter($jobOffer) : new UpdateOfferLetter($jobOffer);
        Notification::send($recipientRecruiter, $notificationRecruiter);

        // Send notification to all admins of the company
        $companyId = $jobOffer->job->company->id;
        $admins = User::allAdmins($companyId);
        Notification::send($admins, new UpdateOfferLetter($jobOffer));

        $candidate = $jobOffer->jobApplication;
        Notification::send($candidate, new UpdateOfferLetter($jobOffer));
    }

}
