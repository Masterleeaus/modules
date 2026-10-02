<?php

namespace Modules\WorksuiteWorkOrders\Filament\Resources;

use Modules\WorksuiteWorkOrders\Filament\Resources\RecurringJobTemplateResource\Pages;
use App\Models\Customer;
use App\Models\Property;
use App\Models\RecurringJobTemplate;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RecurringJobTemplateResource extends Resource
{
    protected static ?string $model = RecurringJobTemplate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static string|\UnitEnum|null $navigationGroup = 'Scheduling';

    protected static ?string $navigationLabel = 'Recurring Jobs';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Schedule Details')->schema([
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
                    ->label('Default Technician')
                    ->options(fn () => User::where('organization_id', auth()->user()?->organization_id)
                        ->role('technician')
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all()
                    )
                    ->searchable()
                    ->placeholder('Unassigned'),
                Select::make('frequency')
                    ->options(RecurringJobTemplate::frequencies())
                    ->required(),
                TextInput::make('recurrence_rule')
                    ->label('Custom RRULE')
                    ->placeholder('FREQ=WEEKLY;BYDAY=MO,WE,FR')
                    ->visible(fn ($get) => $get('frequency') === RecurringJobTemplate::FREQUENCY_CUSTOM)
                    ->columnSpanFull(),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date'),
                TextInput::make('price')
                    ->label('Default Price')
                    ->numeric()
                    ->prefix('$'),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('customer.last_name')
                    ->label('Customer')
                    ->formatStateUsing(fn ($record) => $record->customer?->full_name)
                    ->searchable(['customers.last_name', 'customers.first_name']),
                TextColumn::make('frequency')
                    ->badge()
                    ->formatStateUsing(fn ($state) => RecurringJobTemplate::frequencies()[$state] ?? $state),
                TextColumn::make('start_date')->date()->sortable(),
                TextColumn::make('end_date')->date()->sortable()->placeholder('No end'),
                TextColumn::make('price')->money('usd')->placeholder('—'),
                TextColumn::make('last_generated_on')->label('Last Generated')->date()->sortable()->placeholder('Never'),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Active'),
                SelectFilter::make('frequency')
                    ->options(RecurringJobTemplate::frequencies()),
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
            ->defaultSort('title');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRecurringJobTemplates::route('/'),
            'create' => Pages\CreateRecurringJobTemplate::route('/create'),
            'edit'   => Pages\EditRecurringJobTemplate::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('organization_id', auth()->user()?->organization_id)
            ->with(['customer', 'jobType']);
    }
}
