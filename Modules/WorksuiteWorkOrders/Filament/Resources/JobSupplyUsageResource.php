<?php

namespace Modules\WorksuiteWorkOrders\Filament\Resources;

use Modules\WorksuiteWorkOrders\Filament\Resources\JobSupplyUsageResource\Pages;
use App\Models\Job;
use App\Models\JobSupplyUsage;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JobSupplyUsageResource extends Resource
{
    protected static ?string $model = JobSupplyUsage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static string|\UnitEnum|null $navigationGroup = 'Jobs';

    protected static ?string $navigationLabel = 'Supply Usage';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Supply Usage')->columns(2)->schema([
                Select::make('job_id')
                    ->label('Job')
                    ->relationship('job', 'title', fn (Builder $query) =>
                        $query->where('organization_id', auth()->user()?->organization_id)
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
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
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('job.title')->label('Job')->searchable()->sortable(),
                TextColumn::make('item.name')->label('Supply')->searchable()->sortable(),
                TextColumn::make('quantity_used')->label('Qty')->sortable(),
                TextColumn::make('recordedBy.name')->label('Recorded By')->placeholder('—'),
                TextColumn::make('created_at')->label('Recorded At')->dateTime()->sortable(),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListJobSupplyUsages::route('/'),
            'create' => Pages\CreateJobSupplyUsage::route('/create'),
            'edit'   => Pages\EditJobSupplyUsage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('job', fn ($q) => $q->where('organization_id', auth()->user()?->organization_id));
    }
}
