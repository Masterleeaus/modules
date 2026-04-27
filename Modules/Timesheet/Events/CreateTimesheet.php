<?php

namespace Modules\Timesheet\Events;

use Illuminate\Queue\SerializesModels;

class CreateTimesheet
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $timesheet;

    public function __construct($request,$timesheet)
    {
        $this->request = $request;
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
