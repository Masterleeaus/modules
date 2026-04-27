<?php

namespace Modules\SupplyChain\Filament\Resources;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\SupplyChain\Entities\StockLevel;
use Modules\SupplyChain\Filament\Resources\StockResource\Pages;

class StockResource extends Resource
{
    protected static ?string $model = StockLevel::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';
    protected static string|\UnitEnum|null $navigationGroup = 'Supply Chain';
    protected static ?int $navigationSort = 20;
    protected static ?string $navigationLabel = 'Stock Levels';
    protected static ?string $modelLabel = 'Stock Level';
    protected static ?string $pluralModelLabel = 'Stock Levels';

    public static function canViewAny(): bool
    {
        if (!user()) {
            return false;
        }
        return user()->permission('supplychain.view') === 'all';
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item.name')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label('Warehouse')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('on_hand')
                    ->label('On Hand')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty_reserved')
                    ->label('Reserved')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('qty_available')
                    ->label('Available')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($state, StockLevel $record): string =>
                        $state <= $record->min_qty ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('min_qty')
                    ->label('Min')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('max_qty')
                    ->label('Max')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('low_stock')
                    ->label('Low Stock Only')
                    ->query(fn ($query) => $query->whereColumn('qty_available', '<=', 'min_qty')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('item.name')->label('Item'),
            TextEntry::make('warehouse.name')->label('Warehouse'),
            TextEntry::make('on_hand')->label('On Hand'),
            TextEntry::make('qty_reserved')->label('Reserved'),
            TextEntry::make('qty_available')->label('Available'),
            TextEntry::make('min_qty')->label('Min Qty'),
            TextEntry::make('max_qty')->label('Max Qty'),
            TextEntry::make('updated_at')->dateTime()->label('Last Updated'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockLevels::route('/'),
        ];
    }
}
