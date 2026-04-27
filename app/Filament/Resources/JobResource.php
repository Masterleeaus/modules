<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobResource\Pages;
use App\Filament\Resources\JobResource\RelationManagers\ChecklistItemsRelationManager;
use App\Filament\Resources\JobResource\RelationManagers\LineItemsRelationManager;
use App\Filament\Resources\JobResource\RelationManagers\MessagesRelationManager;
use App\Filament\Resources\JobResource\RelationManagers\SupplyUsagesRelationManager;
use App\Models\Customer;
use App\Models\Job;
use App\Models\Property;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JobResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string|\UnitEnum|null $navigationGroup = 'Jobs';

    protected static ?string $navigationLabel = 'Jobs';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('Job Details')->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Select::make('customer_id')
                    ->label('Customer')
                    ->options(fn () => Customer::where('organization_id', auth()->user()?->organization_id)
                        ->orderBy('last_name')
                        ->get()
                        ->mapWithKeys(fn ($c) => [$c->id => "{$c->last_name}, {$c->first_name}"])
                        ->all()
                    )
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn ($set) => $set('property_id', null))
                    ->required(),
                Select::make('property_id')
                    ->label('Property')
                    ->options(fn ($get) => Property::where('customer_id', $get('customer_id'))
                        ->get()
                        ->mapWithKeys(fn ($p) => [$p->id => $p->full_address])
                        ->all()
                    )
                    ->searchable()
                    ->reactive(),
                Select::make('job_type_id')
                    ->label('Job Type')
                    ->relationship('jobType', 'name', fn (Builder $query) =>
                        $query->where('organization_id', auth()->user()?->organization_id)
                    )
                    ->preload()
                    ->searchable(),
                Select::make('assigned_to')
                    ->label('Assigned Technician')
                    ->options(fn () => User::where('organization_id', auth()->user()?->organization_id)
                        ->role('technician')
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all()
                    )
                    ->searchable()
                    ->placeholder('Unassigned'),
                Select::make('status')
                    ->options(Job::statuses())
                    ->default(Job::STATUS_SCHEDULED)
                    ->required(),
                DateTimePicker::make('scheduled_at')
                    ->label('Scheduled At')
                    ->seconds(false),
                DateTimePicker::make('started_at')
                    ->label('Started At')
                    ->seconds(false),
                DateTimePicker::make('completed_at')
                    ->label('Completed At')
                    ->seconds(false),
            ])->columns(2),

            \Filament\Schemas\Components\Section::make('Notes')->schema([
                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('office_notes')
                    ->label('Office Notes')
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('technician_notes')
                    ->label('Technician Notes')
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('customer_notes')
                    ->label('Customer Notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Job Details')->schema([
                TextEntry::make('title'),
                TextEntry::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled'   => 'info',
                        'en_route'    => 'warning',
                        'in_progress' => 'warning',
                        'completed'   => 'success',
                        'cancelled'   => 'danger',
                        'on_hold'     => 'gray',
                        default       => 'gray',
                    }),
                TextEntry::make('customer.full_name')->label('Customer'),
                TextEntry::make('property.address_line1')->label('Property'),
                TextEntry::make('jobType.name')->label('Job Type'),
                TextEntry::make('assignedTechnician.name')->label('Assigned To'),
                TextEntry::make('scheduled_at')->label('Scheduled')->dateTime(),
                TextEntry::make('started_at')->label('Started')->dateTime(),
                TextEntry::make('completed_at')->label('Completed')->dateTime(),
            ])->columns(2),
            Section::make('Notes')->schema([
                TextEntry::make('description')->prose(),
                TextEntry::make('office_notes')->label('Office Notes')->prose(),
                TextEntry::make('technician_notes')->label('Technician Notes')->prose(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('scheduled_at')
                    ->label('Scheduled')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
                TextColumn::make('title')->searchable()->limit(40),
                TextColumn::make('customer.last_name')
                    ->label('Customer')
                    ->formatStateUsing(fn ($record) => $record->customer?->full_name)
                    ->searchable(['customers.last_name', 'customers.first_name']),
                TextColumn::make('assignedTechnician.name')
                    ->label('Technician')
                    ->placeholder('Unassigned'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled'   => 'info',
                        'en_route'    => 'warning',
                        'in_progress' => 'warning',
                        'completed'   => 'success',
                        'cancelled'   => 'danger',
                        'on_hold'     => 'gray',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Job::statuses()[$state] ?? $state),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Job::statuses()),
                SelectFilter::make('assigned_to')
                    ->label('Technician')
                    ->relationship('assignedTechnician', 'name', fn (Builder $query) =>
                        $query->where('organization_id', auth()->user()?->organization_id)
                    )
                    ->placeholder('All technicians'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('scheduled_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ChecklistItemsRelationManager::class,
            LineItemsRelationManager::class,
            MessagesRelationManager::class,
            SupplyUsagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListJobs::route('/'),
            'create' => Pages\CreateJob::route('/create'),
            'edit'   => Pages\EditJob::route('/{record}/edit'),
            'view'   => Pages\ViewJob::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->where('organization_id', auth()->user()?->organization_id)
            ->with(['customer', 'assignedTechnician', 'jobType']);
    }

    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['organization_id'] = auth()->user()?->organization_id;
        return $data;
    }
}
