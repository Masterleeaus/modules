<?php

namespace Modules\Recruit\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Modules\Recruit\Events\OfferLetterEvent;
use Modules\Recruit\Notifications\AdminNewOfferLetter;
use Modules\Recruit\Notifications\RecruiterOfferLetter;
use Modules\Recruit\Notifications\SendOfferLetter;

class OfferLetterListener
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
    public function handle(OfferLetterEvent $jobOffer)
    {

        $jobOffer->type;
        $companyId = $jobOffer->jobOffer->jobApplication->job->company->id;
        $companyAdmins = User::allAdmins($companyId);
        $adminNotification = new AdminNewOfferLetter($jobOffer->jobOffer);

        if ($jobOffer->type == "save" || $jobOffer->type == "saveSend") {

            // Send notification to all company admins
            Notification::send($companyAdmins, $adminNotification);

            // Send notification to the recruiter
            Notification::send($jobOffer->jobOffer->jobApplication->job->recruiter, new RecruiterOfferLetter($jobOffer->jobOffer));
        }


        $applicant = $jobOffer->jobOffer->jobApplication;

        if ($applicant->email) {
            // Send notification to the candidate
            if ($jobOffer->type == "send" || $jobOffer->type == "saveSend") {
                Notification::send($applicant, new SendOfferLetter($jobOffer->jobOffer));
            }
        }
    }

}
