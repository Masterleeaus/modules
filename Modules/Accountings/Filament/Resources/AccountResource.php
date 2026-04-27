<?php

namespace Modules\Accountings\Filament\Resources;

use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Accountings\Entities\Account;
use Modules\Accountings\Filament\Resources\AccountResource\Pages;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?string $navigationLabel = 'Chart of Accounts';

    protected static ?string $modelLabel = 'Account';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Account Details')
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label('Account Code')
                        ->required()
                        ->maxLength(20)
                        ->unique(
                            table: 'accounts',
                            column: 'code',
                            ignorable: fn ($record) => $record,
                            modifyRuleUsing: fn ($rule, $get) => $rule->where('organization_id', auth()->user()?->organization_id),
                        ),
                    TextInput::make('name')
                        ->label('Account Name')
                        ->required()
                        ->maxLength(191),
                    Select::make('type')
                        ->label('Type')
                        ->options([
                            'revenue'   => 'Revenue',
                            'expense'   => 'Expense',
                            'asset'     => 'Asset',
                            'liability' => 'Liability',
                            'equity'    => 'Equity',
                        ])
                        ->required(),
                    TextInput::make('xero_account_id')
                        ->label('Xero Account ID')
                        ->maxLength(191)
                        ->nullable(),
                    Toggle::make('is_system')
                        ->label('System Account')
                        ->helperText('System accounts cannot be deleted.')
                        ->default(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'revenue'   => 'success',
                        'expense'   => 'danger',
                        'asset'     => 'info',
                        'liability' => 'warning',
                        'equity'    => 'gray',
                        default     => 'gray',
                    })
                    ->sortable(),
                IconColumn::make('is_system')
                    ->label('System')
                    ->boolean(),
                TextColumn::make('xero_account_id')
                    ->label('Xero ID')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'revenue'   => 'Revenue',
                        'expense'   => 'Expense',
                        'asset'     => 'Asset',
                        'liability' => 'Liability',
                        'equity'    => 'Equity',
                    ]),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->before(function (Account $record, Actions\DeleteAction $action) {
                        if ($record->is_system) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Cannot delete system account')
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('code');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit'   => Pages\EditAccount::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('organization_id', auth()->user()?->organization_id);
    }
}
