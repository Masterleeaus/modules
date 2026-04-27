<?php

namespace Modules\SupplyChain\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\SupplyChain\Entities\Item;
use Modules\SupplyChain\Entities\StockLevel;
use Modules\SupplyChain\Entities\Supplier;
use Modules\SupplyChain\Entities\Warehouse;

class StockLevelsWidget extends BaseWidget
{
    protected static ?int $sort = 20;
    protected ?string $heading = 'Supply Chain Overview';

    protected function getStats(): array
    {
        $stats = [];

        try {
            $totalSuppliers = Supplier::count();
            $stats[] = Stat::make('Suppliers', $totalSuppliers)
                ->description('Registered suppliers')
                ->icon('heroicon-o-building-storefront')
                ->color('primary');
        } catch (\Throwable) {
            $stats[] = Stat::make('Suppliers', '—')->color('gray');
        }

        try {
            $totalItems = Item::count();
            $stats[] = Stat::make('Stock Items', $totalItems)
                ->description('Tracked inventory items')
                ->icon('heroicon-o-cube')
                ->color('info');
        } catch (\Throwable) {
            $stats[] = Stat::make('Stock Items', '—')->color('gray');
        }

        try {
            $lowStock = StockLevel::whereColumn('qty_available', '<=', 'min_qty')
                ->where('min_qty', '>', 0)
                ->count();
            $stats[] = Stat::make('Low Stock Alerts', $lowStock)
                ->description('Items at or below minimum')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($lowStock > 0 ? 'danger' : 'success');
        } catch (\Throwable) {
            $stats[] = Stat::make('Low Stock Alerts', '—')->color('gray');
        }

        try {
            $totalWarehouses = Warehouse::where('is_active', true)->count();
            $stats[] = Stat::make('Active Warehouses', $totalWarehouses)
                ->description('Locations tracking stock')
                ->icon('heroicon-o-building-office-2')
                ->color('warning');
        } catch (\Throwable) {
            $stats[] = Stat::make('Active Warehouses', '—')->color('gray');
        }

        return $stats;
    }
}
