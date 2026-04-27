<?php

namespace App\Filament\Resources;

use App\Actions\Invoices\RecordPaymentAction;
use App\Actions\Invoices\VoidInvoiceAction;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Models\Payment;
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

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?string $navigationLabel = 'Invoices';

    protected static ?string $modelLabel = 'Invoice';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Invoice')
                ->columns(2)
                ->schema([
                                        TextInput::make('invoice_number')->label('Invoice #')->maxLength(80),
                    Select::make('status')->label('Status')->options(['draft' => 'Draft', 'sent' => 'Sent', 'paid' => 'Paid', 'partial' => 'Partial', 'overdue' => 'Overdue', 'void' => 'Void'])->required(),
                    TextInput::make('total')->label('Total')->numeric(),
                    TextInput::make('balance_due')->label('Balance Due')->numeric(),
                    DatePicker::make('due_at')->label('Due At'),
                    Textarea::make('notes')->label('Notes')->rows(3)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                                TextColumn::make('invoice_number')->label('Invoice #')->searchable()->sortable(),
                TextColumn::make('status')->label('Status')->badge()->searchable()->sortable(),
                TextColumn::make('total')->label('Total')->money('usd')->sortable(),
                TextColumn::make('balance_due')->label('Balance Due')->money('usd')->sortable(),
                TextColumn::make('due_at')->label('Due')->date()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Actions\Action::make('record_payment')
                    ->label('Record Payment')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (Invoice $record): bool =>
                        ! in_array($record->status, [Invoice::STATUS_VOID, Invoice::STATUS_PAID])
                    )
                    ->form([
                        TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->minValue(0.01),
                        Select::make('method')
                            ->label('Method')
                            ->options([
                                Payment::METHOD_CASH          => 'Cash',
                                Payment::METHOD_CHECK         => 'Check',
                                Payment::METHOD_CARD          => 'Card',
                                Payment::METHOD_BANK_TRANSFER => 'Bank Transfer',
                            ])
                            ->required(),
                        TextInput::make('reference')
                            ->label('Reference')
                            ->maxLength(255)
                            ->nullable(),
                        DatePicker::make('paid_at')
                            ->label('Payment Date')
                            ->required(),
                    ])
                    ->action(function (Invoice $record, array $data): void {
                        app(RecordPaymentAction::class)->execute($record, $data, auth()->id());
                    }),
                Actions\Action::make('void')
                    ->label('Void')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Invoice $record): bool =>
                        ! in_array($record->status, [Invoice::STATUS_VOID, Invoice::STATUS_PAID])
                    )
                    ->action(function (Invoice $record): void {
                        app(VoidInvoiceAction::class)->execute($record);
                    }),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
