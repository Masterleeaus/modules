<?php

namespace Modules\SupplyChain\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\SupplyChain\Events\StockLevelLow;
use Modules\SupplyChain\Services\ReorderService;

class CheckStockLevelsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(ReorderService $reorderService): void
    {
        $reorderService->lowStockLevels()->each(function ($stockLevel) {
            event(new StockLevelLow($stockLevel));
            SendReorderAlertJob::dispatch($stockLevel->id);
        });
    }
}
