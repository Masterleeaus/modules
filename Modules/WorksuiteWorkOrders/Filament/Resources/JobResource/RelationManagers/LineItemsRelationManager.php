<?php

namespace Modules\WorksuiteWorkOrders\Filament\Resources\JobResource\RelationManagers;

use App\Models\Item;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LineItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'lineItems';

    protected static ?string $title = 'Line Items';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('item_id')
                ->label('Catalogue Item')
                ->relationship('item', 'name')
                ->searchable()
                ->preload()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $item = Item::find($state);
                    if ($item) {
                        $set('name', $item->name);
                        $set('description', $item->description);
                        $set('unit_price', $item->unit_price);
                    }
                }),
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            TextInput::make('description')
                ->maxLength(500),
            TextInput::make('unit_price')
                ->label('Unit Price')
                ->numeric()
                ->prefix('$')
                ->required(),
            TextInput::make('quantity')
                ->numeric()
                ->default(1)
                ->required(),
            TextInput::make('sort_order')
                ->label('Sort Order')
                ->numeric()
                ->default(0),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')->label('#')->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('quantity'),
                TextColumn::make('unit_price')->label('Unit Price')->money('usd'),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                Actions\CreateAction::make(),
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
