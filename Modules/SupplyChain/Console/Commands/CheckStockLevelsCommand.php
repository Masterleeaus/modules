<?php

namespace Modules\SupplyChain\Console\Commands;

use Illuminate\Console\Command;
use Modules\SupplyChain\Jobs\CheckStockLevelsJob;

class CheckStockLevelsCommand extends Command
{
    protected $signature   = 'supplychain:check-stock {--queue : Dispatch as background job}';
    protected $description = 'Check all stock levels and fire StockLevelLow events for items below minimum qty';

    public function handle(): int
    {
        if ($this->option('queue')) {
            CheckStockLevelsJob::dispatch();
            $this->info('CheckStockLevelsJob dispatched to queue.');
        } else {
            $this->info('Running stock level check synchronously…');
            app()->call([new CheckStockLevelsJob(), 'handle']);
            $this->info('Stock level check complete.');
        }

        return self::SUCCESS;
    }
}
