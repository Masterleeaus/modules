<?php

namespace Modules\CleanQuality\Filament\Resources;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\CleanQuality\Entities\Inspection;
use Modules\CleanQuality\Filament\Resources\InspectionResource\Pages\ListInspections;
use Modules\CleanQuality\Filament\Resources\InspectionResource\Pages\ViewInspection;
use Modules\CleanQuality\Support\Enums\InspectionStatus;
use Modules\CleanQuality\Support\InspectionPermissions;

class InspectionResource extends Resource
{
    protected static ?string $model = Inspection::class;
    protected static ?string $slug = 'clean-quality/inspections';
    protected static ?string $recordTitleAttribute = 'id';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static string|\UnitEnum|null $navigationGroup = 'Quality';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Inspections';
    protected static ?string $modelLabel = 'Inspection';
    protected static ?string $pluralModelLabel = 'Inspections';

    public static function canViewAny(): bool
    {
        $user = function_exists('user') ? user() : auth()->user();

        if (! $user) {
            return false;
        }

        return $user->can(InspectionPermissions::VIEW)
            || $user->can(InspectionPermissions::LEGACY_VIEW);
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool { return false; }

    public static function canEdit(Model $record): bool { return false; }

    public static function canDelete(Model $record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('template.name')
                    ->label('Template')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('inspector.name')
                    ->label('Inspector')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        InspectionStatus::PASSED         => 'success',
                        InspectionStatus::FAILED         => 'danger',
                        InspectionStatus::RECLEAN_BOOKED => 'warning',
                        InspectionStatus::IN_PROGRESS    => 'info',
                        default                          => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->numeric(2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('inspected_at')
                    ->label('Inspected')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        InspectionStatus::PENDING         => 'Pending',
                        InspectionStatus::IN_PROGRESS     => 'In Progress',
                        InspectionStatus::PASSED          => 'Passed',
                        InspectionStatus::FAILED          => 'Failed',
                        InspectionStatus::RECLEAN_BOOKED  => 'Reclean Booked',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Inspection Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('id')->label('ID'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            InspectionStatus::PASSED         => 'success',
                            InspectionStatus::FAILED         => 'danger',
                            InspectionStatus::RECLEAN_BOOKED => 'warning',
                            InspectionStatus::IN_PROGRESS    => 'info',
                            default                          => 'gray',
                        }),
                    TextEntry::make('template.name')->label('Template'),
                    TextEntry::make('inspector.name')->label('Inspector'),
                    TextEntry::make('score')->numeric(2),
                    TextEntry::make('inspected_at')->label('Inspected At')->dateTime(),
                ]),
            Section::make('Outcome')
                ->columns(2)
                ->schema([
                    TextEntry::make('notes')->columnSpanFull(),
                    TextEntry::make('approvedBy.name')->label('Approved By'),
                    TextEntry::make('approved_at')->label('Approved At')->dateTime(),
                    TextEntry::make('reclean_booking_id')->label('Reclean Booking ID'),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInspections::route('/'),
            'view'  => ViewInspection::route('/{record}'),
        ];
    }
}

