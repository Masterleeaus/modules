<?php

namespace Modules\Recruit\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Recruit\Entities\RecruitJobOfferLetter;

class OfferLetterEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $jobOffer;

    public $type;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(RecruitJobOfferLetter $jobOffer, $type)
    {
        $this->jobOffer = $jobOffer;
        $this->type = $type;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
