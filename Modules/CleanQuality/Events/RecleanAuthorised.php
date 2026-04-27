<?php

namespace Modules\CleanQuality\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\CleanQuality\Entities\Inspection;

class RecleanAuthorised
{
    use Dispatchable, SerializesModels;

    public function __construct(public Inspection $inspection)
    {
    }
}

