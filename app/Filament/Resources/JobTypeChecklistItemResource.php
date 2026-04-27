<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobTypeChecklistItemResource\Pages;
use App\Models\JobTypeChecklistItem;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JobTypeChecklistItemResource extends Resource
{
    protected static ?string $model = JobTypeChecklistItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Jobs';

    protected static ?string $navigationLabel = 'Job Type Checklist Items';

    protected static ?string $modelLabel = 'Job Type Checklist Item';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Job Type Checklist Item')
                ->columns(2)
                ->schema([
                                        TextInput::make('job_type_id')->label('Job Type ID')->numeric(),
                    TextInput::make('label')->label('Label')->required()->maxLength(200),
                    TextInput::make('sort_order')->label('Sort Order')->numeric(),
                    Toggle::make('is_required')->label('Required'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                                TextColumn::make('job_type_id')->label('Job Type')->searchable()->sortable(),
                TextColumn::make('label')->label('Label')->searchable()->sortable(),
                TextColumn::make('sort_order')->label('Sort')->searchable()->sortable(),
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
            'index' => Pages\ListJobTypeChecklistItems::route('/'),
            'create' => Pages\CreateJobTypeChecklistItem::route('/create'),
            'edit' => Pages\EditJobTypeChecklistItem::route('/{record}/edit'),
        ];
    }
}
