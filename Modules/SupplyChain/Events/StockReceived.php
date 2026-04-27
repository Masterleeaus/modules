<?php

namespace Modules\SupplyChain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\SupplyChain\Entities\GoodsReceipt;

class StockReceived
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public GoodsReceipt $goodsReceipt)
    {
    }
}
