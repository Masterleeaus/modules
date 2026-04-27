<?php

namespace Modules\Recruit\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Recruit\Entities\RecruitInterviewSchedule;
use Modules\Recruit\Entities\RecruitJobApplication;

class CandidateInterviewScheduleEvent
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $interview;
    public $jobApplication;
    public $candidateComment;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public function __construct(RecruitInterviewSchedule $interview, $jobApplication, $candidateComment)
    {
        $this->interview = $interview;
        $this->jobApplication = $jobApplication;
        $this->candidateComment = $candidateComment;
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
