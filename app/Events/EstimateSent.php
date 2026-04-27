<?php

namespace App\Events;

use App\Models\Estimate;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EstimateSent
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Estimate $estimate) {}
}
