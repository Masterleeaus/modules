<?php

namespace App\Actions\Estimates;

use App\Events\EstimateSent;
use App\Models\Estimate;

class SendEstimateAction
{
    /**
     * Mark an estimate as sent and dispatch the EstimateSent event.
     */
    public function execute(Estimate $estimate): Estimate
    {
        $estimate->update([
            'status'  => Estimate::STATUS_SENT,
            'sent_at' => now(),
        ]);

        EstimateSent::dispatch($estimate);

        return $estimate->fresh();
    }
}
