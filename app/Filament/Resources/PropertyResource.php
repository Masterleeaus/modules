<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Customer;
use App\Models\Property;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static string|\UnitEnum|null $navigationGroup = 'Customers & Properties';

    protected static ?string $navigationLabel = 'Properties';

    protected static ?int $navigationSort = 20;


    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('customer_id')
                ->label('Customer')
                ->relationship('customer', 'last_name', fn (Builder $query) =>
                    $query->where('organization_id', auth()->user()?->organization_id)
                )
                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->last_name}, {$record->first_name}")
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('name')
                ->label('Property Label')
                ->maxLength(255),
            TextInput::make('address_line1')
                ->label('Street Address')
                ->required()
                ->maxLength(255),
            TextInput::make('address_line2')
                ->label('Apt / Suite')
                ->maxLength(255),
            TextInput::make('city')->required()->maxLength(100),
            TextInput::make('state')->required()->maxLength(50),
            TextInput::make('postal_code')
                ->label('ZIP Code')
                ->required()
                ->maxLength(20),
            Textarea::make('notes')->rows(3)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.last_name')
                    ->label('Customer')
                    ->formatStateUsing(fn ($record) => "{$record->customer?->last_name}, {$record->customer?->first_name}")
                    ->searchable(['customers.last_name', 'customers.first_name'])
                    ->sortable(),
                TextColumn::make('address_line1')
                    ->label('Address')
                    ->searchable(),
                TextColumn::make('city')->searchable()->sortable(),
                TextColumn::make('state')->sortable(),
                TextColumn::make('postal_code')->label('ZIP'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit'   => Pages\EditProperty::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->where('organization_id', auth()->user()?->organization_id);
    }
}
