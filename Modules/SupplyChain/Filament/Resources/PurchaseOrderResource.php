<?php

namespace Modules\SupplyChain\Filament\Resources;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\SupplyChain\Entities\PurchaseOrder;
use Modules\SupplyChain\Entities\Supplier;
use Modules\SupplyChain\Filament\Resources\PurchaseOrderResource\Pages;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string|\UnitEnum|null $navigationGroup = 'Supply Chain';
    protected static ?int $navigationSort = 30;
    protected static ?string $navigationLabel = 'Purchase Orders';
    protected static ?string $modelLabel = 'Purchase Order';
    protected static ?string $pluralModelLabel = 'Purchase Orders';

    public static function canViewAny(): bool
    {
        if (!user()) {
            return false;
        }
        return user()->permission('supplychain.purchasing.view') === 'all'
            || user()->permission('supplychain.view') === 'all';
    }

    public static function canCreate(): bool
    {
        return user() && user()->permission('supplychain.purchasing.manage') === 'all';
    }

    public static function canEdit(Model $record): bool
    {
        return static::canCreate();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canCreate();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('supplier_id')
                ->label('Supplier')
                ->options(fn () => Supplier::orderBy('name')->pluck('name', 'id'))
                ->required()
                ->searchable(),
            TextInput::make('reference')
                ->label('Reference / PO#')
                ->maxLength(190),
            DatePicker::make('expected_date')
                ->label('Expected Delivery'),
            Select::make('currency')
                ->options(['AUD' => 'AUD', 'USD' => 'USD', 'GBP' => 'GBP', 'EUR' => 'EUR'])
                ->default('AUD'),
            Textarea::make('notes')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ordered'  => 'primary',
                        'received' => 'success',
                        'cancelled'=> 'danger',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total')
                    ->money('AUD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ordered_at')
                    ->label('Ordered')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_date')
                    ->label('Expected')
                    ->date()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'ordered'  => 'Ordered',
                        'received' => 'Received',
                        'cancelled'=> 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('ordered_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('reference')->label('Reference'),
            TextEntry::make('supplier.name')->label('Supplier'),
            TextEntry::make('status')->badge(),
            TextEntry::make('total')->money('AUD'),
            TextEntry::make('currency'),
            TextEntry::make('ordered_at')->dateTime()->label('Ordered At'),
            TextEntry::make('expected_date')->date()->label('Expected'),
            TextEntry::make('orderedBy.name')->label('Ordered By'),
            TextEntry::make('notes')->columnSpanFull(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'view'   => Pages\ViewPurchaseOrder::route('/{record}'),
        ];
    }
}
