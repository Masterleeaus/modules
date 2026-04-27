<?php

namespace Modules\SupplyChain\Filament\Plugin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\SupplyChain\Filament\Resources\PurchaseOrderResource;
use Modules\SupplyChain\Filament\Resources\StockResource;
use Modules\SupplyChain\Filament\Resources\SupplierResource;
use Modules\SupplyChain\Filament\Widgets\LowStockAlertsWidget;
use Modules\SupplyChain\Filament\Widgets\StockLevelsWidget;

class SupplyChainPlugin implements Plugin
{
    public static function make(): static
    {
        return new static();
    }

    public function getId(): string
    {
        return 'supplychain';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                SupplierResource::class,
                StockResource::class,
                PurchaseOrderResource::class,
            ])
            ->widgets([
                StockLevelsWidget::class,
                LowStockAlertsWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
    }
}
