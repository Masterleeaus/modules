<?php

namespace Modules\SupplyChain\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\SupplyChain\Actions\PlacePurchaseOrder;

class GeneratePurchaseOrderJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public array $payload)
    {
    }

    public function handle(PlacePurchaseOrder $placePurchaseOrder): void
    {
        $placePurchaseOrder->execute($this->payload);
    }
}
