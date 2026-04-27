<?php

namespace Modules\SupplyChain\Listeners;

use Modules\SupplyChain\Events\StockLevelLow;
use Modules\SupplyChain\Jobs\SendReorderAlertJob;

class QueueReorderAlertListener
{
    public function handle(StockLevelLow $event): void
    {
        SendReorderAlertJob::dispatch($event->stockLevel->id);
    }
}
