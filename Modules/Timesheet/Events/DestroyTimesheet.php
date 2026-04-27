<?php

namespace Modules\Timesheet\Events;

use Illuminate\Queue\SerializesModels;

class DestroyTimesheet
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $timesheet;

    public function __construct($timesheet)
    {
        $this->timesheet = $timesheet;
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
