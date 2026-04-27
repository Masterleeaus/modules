<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
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

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?string $navigationLabel = 'Payments';

    protected static ?string $modelLabel = 'Payment';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Payment')
                ->columns(2)
                ->schema([
                                        TextInput::make('invoice_id')->label('Invoice ID')->numeric(),
                    TextInput::make('amount')->label('Amount')->required()->numeric(),
                    Select::make('method')->label('Method')->options(['cash' => 'Cash', 'check' => 'Check', 'card' => 'Card', 'bank_transfer' => 'Bank Transfer', 'stripe' => 'Stripe'])->required(),
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
                                TextColumn::make('invoice_id')->label('Invoice')->searchable()->sortable(),
                TextColumn::make('amount')->label('Amount')->money('usd')->sortable(),
                TextColumn::make('method')->label('Method')->badge()->searchable()->sortable(),
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
