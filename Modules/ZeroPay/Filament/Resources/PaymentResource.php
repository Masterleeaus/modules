<?php

namespace Modules\ZeroPay\Filament\Resources;

use App\Models\Payment;
use Filament\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\ZeroPay\Filament\Resources\PaymentResource\Pages\CreatePayment;
use Modules\ZeroPay\Filament\Resources\PaymentResource\Pages\EditPayment;
use Modules\ZeroPay\Filament\Resources\PaymentResource\Pages\ListPayments;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $slug = 'zero-pay/payments';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|\UnitEnum|null $navigationGroup = 'Payments';

    protected static ?string $navigationLabel = 'Payments';

    protected static ?string $modelLabel = 'Payment';

    protected static ?int $navigationSort = 10;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('organization_id', auth()->user()?->organization_id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Payment')
                ->columns(2)
                ->schema([
                    TextInput::make('invoice_id')->label('Invoice ID')->numeric(),
                    TextInput::make('amount')->label('Amount')->required()->numeric(),
                    Select::make('method')
                        ->label('Method')
                        ->options([
                            Payment::METHOD_CASH         => 'Cash',
                            Payment::METHOD_CHECK        => 'Cheque',
                            Payment::METHOD_CARD         => 'Card',
                            Payment::METHOD_BANK_TRANSFER => 'Bank Transfer',
                            Payment::METHOD_STRIPE       => 'Stripe',
                        ])
                        ->required(),
                    TextInput::make('reference')->label('Reference')->maxLength(160),
                    DateTimePicker::make('paid_at')->label('Paid At'),
                    Textarea::make('notes')->label('Notes')->rows(3)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice.invoice_number')->label('Invoice #')->searchable()->sortable(),
                TextColumn::make('amount')->label('Amount')->money('usd')->sortable(),
                TextColumn::make('method')
                    ->label('Method')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('paid_at')->label('Paid At')->dateTime()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Actions\EditAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('paid_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPayments::route('/'),
            'create' => CreatePayment::route('/create'),
            'edit'   => EditPayment::route('/{record}/edit'),
        ];
    }
}
