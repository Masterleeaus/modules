<?php

namespace App\Filament\Resources\JobResource\RelationManagers;

use App\Models\Item;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SupplyUsagesRelationManager extends RelationManager
{
    protected static string $relationship = 'supplyUsages';

    protected static ?string $title = 'Supply Usage';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('item_id')
                ->label('Supply Item')
                ->relationship('item', 'name')
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('quantity_used')
                ->label('Quantity Used')
                ->numeric()
                ->default(1)
                ->required(),
            Textarea::make('notes')
                ->rows(2)
                ->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item.name')->label('Supply')->searchable(),
                TextColumn::make('quantity_used')->label('Qty'),
                TextColumn::make('notes')->limit(50)->placeholder('—'),
                TextColumn::make('recordedBy.name')->label('Recorded By')->placeholder('—'),
                TextColumn::make('created_at')->label('Date')->date(),
            ])
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
