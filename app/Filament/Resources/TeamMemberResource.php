<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamMemberResource\Pages;
use App\Models\User;
use App\Services\PlanService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class TeamMemberResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Team';

    protected static ?string $modelLabel = 'Team Member';

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            TextInput::make('password')
                ->password()
                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $operation): bool => $operation === 'create')
                ->maxLength(255),
            Select::make('roles')
                ->label('Role')
                ->options([
                    'owner'       => 'Owner',
                    'admin'       => 'Admin',
                    'dispatcher'  => 'Dispatcher',
                    'bookkeeper'  => 'Bookkeeper',
                    'technician'  => 'Technician',
                ])
                ->required()
                ->dehydrated(false)
                ->afterStateHydrated(function ($component, $record) {
                    if ($record) {
                        $component->state($record->getRoleNames()->first());
                    }
                }),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'owner'      => 'warning',
                        'admin'      => 'danger',
                        'dispatcher' => 'info',
                        'bookkeeper' => 'primary',
                        'technician' => 'success',
                        default      => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->options([
                        'owner'       => 'Owner',
                        'admin'       => 'Admin',
                        'dispatcher'  => 'Dispatcher',
                        'bookkeeper'  => 'Bookkeeper',
                        'technician'  => 'Technician',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->using(function (User $record, array $data) {
                        $record->update([
                            'name'  => $data['name'],
                            'email' => $data['email'],
                        ]);
                        if (isset($data['password'])) {
                            $record->update(['password' => $data['password']]);
                        }
                        if (isset($data['roles'])) {
                            $record->syncRoles([$data['roles']]);
                        }
                        return $record;
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            // Prevent deleting yourself
                            $records->reject(fn (User $u) => $u->id === auth()->id());
                        }),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTeamMembers::route('/'),
            'create' => Pages\CreateTeamMember::route('/create'),
            'edit'   => Pages\EditTeamMember::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('organization_id', auth()->user()?->organization_id)
            ->with('roles');
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['organization_id'] = auth()->user()?->organization_id;
        return $data;
    }
}
