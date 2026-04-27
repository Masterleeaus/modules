<?php

namespace Modules\Recruit\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Recruit\Entities\RecruitJob;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Modules\Recruit\Entities\RecruitJobApplication;

class NewJobApplicationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $jobApplication;
    public $job;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(RecruitJobApplication $jobApplication, $job)
    {
        $this->jobApplication = $jobApplication;
        $this->job= $job;
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
