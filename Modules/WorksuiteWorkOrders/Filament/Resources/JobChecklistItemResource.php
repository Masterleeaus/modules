<?php

namespace Modules\WorksuiteWorkOrders\Filament\Resources;

use Modules\WorksuiteWorkOrders\Filament\Resources\JobChecklistItemResource\Pages;
use App\Models\JobChecklistItem;
use Filament\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JobChecklistItemResource extends Resource
{
    protected static ?string $model = JobChecklistItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-check-circle';

    protected static string|\UnitEnum|null $navigationGroup = 'Jobs';

    protected static ?string $navigationLabel = 'Job Checklist Items';

    protected static ?string $modelLabel = 'Job Checklist Item';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Job Checklist Item')
                ->columns(2)
                ->schema([
                                        TextInput::make('job_id')->label('Job ID')->numeric(),
                    TextInput::make('label')->label('Label')->required()->maxLength(200),
                    TextInput::make('sort_order')->label('Sort Order')->numeric(),
                    Toggle::make('is_required')->label('Required'),
                    DateTimePicker::make('completed_at')->label('Completed At'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                                TextColumn::make('job_id')->label('Job')->searchable()->sortable(),
                TextColumn::make('label')->label('Label')->searchable()->sortable(),
                TextColumn::make('completed_at')->label('Completed')->dateTime()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Actions\EditAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobChecklistItems::route('/'),
            'create' => Pages\CreateJobChecklistItem::route('/create'),
            'edit' => Pages\EditJobChecklistItem::route('/{record}/edit'),
        ];
    }
}
