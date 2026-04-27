<?php

namespace Modules\Accountings\Filament\Resources;

use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Accountings\Entities\JournalEntry;
use Modules\Accountings\Filament\Resources\JournalEntryResource\Pages;

class JournalEntryResource extends Resource
{
    protected static ?string $model = JournalEntry::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?string $navigationLabel = 'Journal Entries';

    protected static ?string $modelLabel = 'Journal Entry';

    protected static ?int $navigationSort = 20;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('entry_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('reference_type')
                    ->label('Reference Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('reference_id')
                    ->label('Reference ID')
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('reference_type')
                    ->options([
                        'Invoice' => 'Invoice',
                        'Payment' => 'Payment',
                    ]),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
            ])
            ->toolbarActions([])
            ->defaultSort('entry_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJournalEntries::route('/'),
            'view'  => Pages\ViewJournalEntry::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('organization_id', auth()->user()?->organization_id)
            ->with('lines.account');
    }
}
