<?php

namespace Modules\SupplyChain\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Modules\SupplyChain\Entities\StockLevel;

class LowStockAlertsWidget extends BaseWidget
{
    protected static ?int $sort = 21;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Low Stock Alerts';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                StockLevel::query()
                    ->with(['item', 'warehouse'])
                    ->whereColumn('qty_available', '<=', 'min_qty')
                    ->where('min_qty', '>', 0)
            )
            ->columns([
                Tables\Columns\TextColumn::make('item.name')
                    ->label('Item')
                    ->searchable(),
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label('Warehouse'),
                Tables\Columns\TextColumn::make('qty_available')
                    ->label('Available')
                    ->numeric()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('on_hand')
                    ->label('On Hand')
                    ->numeric(),
                Tables\Columns\TextColumn::make('min_qty')
                    ->label('Min Qty')
                    ->numeric(),
            ]);
    }
}
