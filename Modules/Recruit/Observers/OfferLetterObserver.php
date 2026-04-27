<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\OfferLetterHistory;
use Modules\Recruit\Entities\RecruitJobHistory;
use Modules\Recruit\Entities\RecruitJobOfferLetter;
use Modules\Recruit\Events\UpdateOfferLetterEvent;

class OfferLetterObserver
{

    public function saving(RecruitJobOfferLetter $event)
    {
        if (! isRunningInConsoleOrSeeding() && user()) {
            $event->last_updated_by = user()->id;
        }
    }

    public function creating(RecruitJobOfferLetter $event)
    {
        if (! isRunningInConsoleOrSeeding() && user()) {
            $event->added_by = user()->id;
        }
    }

    public function created(RecruitJobOfferLetter $event)
    {
        if (\user()) {
            $this->logOfferLetterActivity($event->recruit_job_id, $event->id, user()->id, 'createLetter');
        }
    }

    public function updated(RecruitJobOfferLetter $event)
    {
        if (\user()) {
            if ($event->status == 'accept') {
                $this->logOfferLetterActivity($event->recruit_job_id, $event->id, user()->id, 'acceptLetter');

            } elseif ($event->status == 'decline') {
                $this->logOfferLetterActivity($event->recruit_job_id, $event->id, user()->id, 'declineLetter');

            } else {
                $this->logOfferLetterActivity($event->recruit_job_id, $event->id, user()->id, 'updateLetter');
            }
        }

        event(new UpdateOfferLetterEvent($event));
    }

    public function logOfferLetterActivity($jobId, $letterID, $userID, $text)
    {
        $activity = new OfferLetterHistory;

        if (! is_null($letterID)) {
            $activity->recruit_job_offer_letter_id = $letterID;
        }

        $activity->user_id = $userID;
        $activity->details = $text;
        $activity->save();

        $activity = new RecruitJobHistory;

        if (! is_null($letterID)) {
            $activity->recruit_job_offer_letter_id = $letterID;
        }

        $activity->user_id = $userID;
        $activity->recruit_job_id = $jobId;
        $activity->details = $text;
        $activity->save();
    }

}
