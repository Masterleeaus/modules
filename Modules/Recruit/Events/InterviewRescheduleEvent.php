<?php

namespace Modules\Recruit\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Recruit\Entities\RecruitInterviewSchedule;

class InterviewRescheduleEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $interview;

    public $employee;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(RecruitInterviewSchedule $interview, $employee)
    {
        $this->interview = $interview;
        $this->employee = $employee;
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
