<?php

namespace Modules\Recruit\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Recruit\Entities\RecruitInterviewSchedule;

class HostInterviewEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $interviewSchedule;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(RecruitInterviewSchedule $interviewSchedule)
    {
        $this->interviewSchedule = $interviewSchedule;
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
