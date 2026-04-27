<?php

namespace Modules\Recruit\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Recruit\Entities\RecruitJobApplication;

class UpdateJobApplicationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $jobApplication;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(RecruitJobApplication $jobApplication)
    {
        $this->jobApplication = $jobApplication;
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
