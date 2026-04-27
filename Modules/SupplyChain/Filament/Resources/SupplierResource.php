<?php

namespace Modules\SupplyChain\Filament\Resources;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\SupplyChain\Entities\Supplier;
use Modules\SupplyChain\Filament\Resources\SupplierResource\Pages;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';
    protected static string|\UnitEnum|null $navigationGroup = 'Supply Chain';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationLabel = 'Suppliers';
    protected static ?string $modelLabel = 'Supplier';
    protected static ?string $pluralModelLabel = 'Suppliers';

    public static function canViewAny(): bool
    {
        if (!user()) {
            return false;
        }
        return user()->permission('supplychain.suppliers.view') === 'all'
            || user()->permission('supplychain.view') === 'all';
    }

    public static function canCreate(): bool
    {
        return user() && user()->permission('supplychain.suppliers.manage') === 'all';
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
            TextInput::make('name')
                ->required()
                ->maxLength(191)
                ->columnSpanFull(),
            TextInput::make('email')->email()->maxLength(191),
            TextInput::make('phone')->tel()->maxLength(50),
            TextInput::make('abn')->label('ABN')->maxLength(20),
            TextInput::make('website')->url()->maxLength(191),
            TextInput::make('contact_person')->maxLength(191),
            TextInput::make('address')->label('Address')->maxLength(255),
            TextInput::make('fsm_rating')
                ->label('FSM Rating (1–5)')
                ->numeric()
                ->minValue(1)
                ->maxValue(5),
            TextInput::make('fsm_lead_time_days')
                ->label('Lead Time (days)')
                ->numeric()
                ->minValue(0),
            TextInput::make('fsm_payment_terms')
                ->label('Payment Terms')
                ->maxLength(191),
            Textarea::make('notes')->columnSpanFull(),
            Toggle::make('is_active')->label('Active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('abn')
                    ->label('ABN')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fsm_rating')
                    ->label('Rating')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default     => 'danger',
                    }),
                Tables\Columns\TextColumn::make('fsm_lead_time_days')
                    ->label('Lead Days')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
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
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('name'),
            TextEntry::make('email'),
            TextEntry::make('phone'),
            TextEntry::make('abn')->label('ABN'),
            TextEntry::make('contact_person')->label('Contact Person'),
            TextEntry::make('address'),
            TextEntry::make('fsm_rating')->label('FSM Rating'),
            TextEntry::make('fsm_lead_time_days')->label('Lead Time (days)'),
            TextEntry::make('fsm_payment_terms')->label('Payment Terms'),
            TextEntry::make('notes')->columnSpanFull(),
            TextEntry::make('is_active')
                ->label('Active')
                ->badge()
                ->color(fn ($state): string => $state ? 'success' : 'danger'),
            TextEntry::make('created_at')->dateTime(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'view'   => Pages\ViewSupplier::route('/{record}'),
            'edit'   => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
