<?php

namespace Modules\SupplyChain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\SupplyChain\Entities\StockLevel;

class StockLevelLow
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public StockLevel $stockLevel)
    {
    }
}
