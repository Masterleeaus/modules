<?php

namespace Modules\CleanQuality\Filament\Resources;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\CleanQuality\Entities\QcRecord;
use Modules\CleanQuality\Filament\Resources\QualityCheckResource\Pages\ListQualityChecks;
use Modules\CleanQuality\Filament\Resources\QualityCheckResource\Pages\ViewQualityCheck;
use Modules\CleanQuality\Support\InspectionPermissions;

class QualityCheckResource extends Resource
{
    protected static ?string $model = QcRecord::class;
    protected static ?string $slug = 'clean-quality/quality-checks';
    protected static ?string $recordTitleAttribute = 'id';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-check-badge';
    protected static string|\UnitEnum|null $navigationGroup = 'Quality';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'QC Records';
    protected static ?string $modelLabel = 'QC Record';
    protected static ?string $pluralModelLabel = 'QC Records';

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
                Tables\Columns\TextColumn::make('cleaner.name')
                    ->label('Cleaner')
                    ->searchable(),
                Tables\Columns\TextColumn::make('template.name')
                    ->label('Template')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pass'              => 'success',
                        'fail'              => 'danger',
                        'reclean_required'  => 'warning',
                        'reclean_done'      => 'info',
                        default             => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('overall_score')
                    ->label('Score')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('reclean_triggered')
                    ->label('Reclean')
                    ->boolean(),
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
                    ->options(array_combine(QcRecord::STATUSES, array_map(
                        fn (string $s) => ucwords(str_replace('_', ' ', $s)),
                        QcRecord::STATUSES
                    ))),
                TernaryFilter::make('reclean_triggered')
                    ->label('Reclean Triggered'),
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
            Section::make('QC Record Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('id')->label('ID'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'pass'             => 'success',
                            'fail'             => 'danger',
                            'reclean_required' => 'warning',
                            'reclean_done'     => 'info',
                            default            => 'gray',
                        }),
                    TextEntry::make('cleaner.name')->label('Cleaner'),
                    TextEntry::make('inspector.name')->label('Inspector'),
                    TextEntry::make('template.name')->label('Template'),
                    TextEntry::make('overall_score')->label('Overall Score'),
                    TextEntry::make('inspected_at')->label('Inspected At')->dateTime(),
                ]),
            Section::make('Reclean & Outcome')
                ->columns(2)
                ->schema([
                    IconEntry::make('reclean_triggered')->label('Reclean Triggered')->boolean(),
                    TextEntry::make('reclean_triggered_at')->label('Reclean At')->dateTime(),
                    TextEntry::make('reclean_job_id')->label('Reclean Job ID'),
                    TextEntry::make('complaint_id')->label('Complaint ID'),
                    TextEntry::make('notes')->columnSpanFull(),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQualityChecks::route('/'),
            'view'  => ViewQualityCheck::route('/{record}'),
        ];
    }
}

