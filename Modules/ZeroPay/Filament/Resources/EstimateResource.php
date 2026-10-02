<?php

namespace Modules\ZeroPay\Filament\Resources;

use App\Actions\Estimates\ConvertEstimateToJobAction;
use App\Actions\Estimates\SendEstimateAction;
use App\Models\Estimate;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\ZeroPay\Filament\Resources\EstimateResource\Pages\CreateEstimate;
use Modules\ZeroPay\Filament\Resources\EstimateResource\Pages\EditEstimate;
use Modules\ZeroPay\Filament\Resources\EstimateResource\Pages\ListEstimates;

class EstimateResource extends Resource
{
    protected static ?string $model = Estimate::class;

    protected static ?string $slug = 'zero-pay/estimates';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Estimates';

    protected static ?string $navigationLabel = 'Estimates';

    protected static ?string $modelLabel = 'Estimate';

    protected static ?int $navigationSort = 10;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('organization_id', auth()->user()?->organization_id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Estimate')
                ->columns(2)
                ->schema([
                    TextInput::make('estimate_number')->label('Estimate #')->maxLength(80),
                    TextInput::make('title')->label('Title')->required()->maxLength(160),
                    Select::make('status')
                        ->label('Status')
                        ->options(Estimate::statuses())
                        ->required(),
                    TextInput::make('tax_rate')->label('Tax Rate')->numeric(),
                    DatePicker::make('expires_at')->label('Expires At'),
                    Textarea::make('intro')->label('Intro')->rows(3)->columnSpanFull(),
                    Textarea::make('footer')->label('Footer')->rows(3)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('estimate_number')->label('Estimate #')->searchable()->sortable(),
                TextColumn::make('title')->label('Title')->searchable()->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'accepted' => 'success',
                        'declined' => 'danger',
                        'expired'  => 'warning',
                        'sent'     => 'info',
                        default    => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('expires_at')->label('Expires')->date()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Actions\Action::make('send')
                    ->label('Send')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Estimate $record): bool => $record->status === Estimate::STATUS_DRAFT)
                    ->action(function (Estimate $record): void {
                        app(SendEstimateAction::class)->execute($record);
                    }),
                Actions\Action::make('convert_to_job')
                    ->label('Convert to Job')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Estimate $record): bool =>
                        $record->status === Estimate::STATUS_ACCEPTED && $record->convertedJob === null
                    )
                    ->action(function (Estimate $record): void {
                        app(ConvertEstimateToJobAction::class)->execute($record);
                    }),
                Actions\EditAction::make(),
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
            'index'  => ListEstimates::route('/'),
            'create' => CreateEstimate::route('/create'),
            'edit'   => EditEstimate::route('/{record}/edit'),
        ];
    }
}
