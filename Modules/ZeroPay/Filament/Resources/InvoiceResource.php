<?php

namespace Modules\ZeroPay\Filament\Resources;

use App\Models\Invoice;
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
use Modules\ZeroPay\Filament\Resources\InvoiceResource\Pages\CreateInvoice;
use Modules\ZeroPay\Filament\Resources\InvoiceResource\Pages\EditInvoice;
use Modules\ZeroPay\Filament\Resources\InvoiceResource\Pages\ListInvoices;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $slug = 'zero-pay/invoices';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static string|\UnitEnum|null $navigationGroup = 'Invoices';

    protected static ?string $navigationLabel = 'Invoices';

    protected static ?string $modelLabel = 'Invoice';

    protected static ?int $navigationSort = 10;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('organization_id', auth()->user()?->organization_id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Invoice')
                ->columns(2)
                ->schema([
                    TextInput::make('invoice_number')->label('Invoice #')->maxLength(80),
                    Select::make('status')
                        ->label('Status')
                        ->options(Invoice::statuses())
                        ->required(),
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
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid'    => 'success',
                        'overdue' => 'danger',
                        'partial' => 'warning',
                        'sent'    => 'info',
                        default   => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total')->label('Total')->money('usd')->sortable(),
                TextColumn::make('balance_due')->label('Balance Due')->money('usd')->sortable(),
                TextColumn::make('due_at')->label('Due')->date()->sortable(),
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListInvoices::route('/'),
            'create' => CreateInvoice::route('/create'),
            'edit'   => EditInvoice::route('/{record}/edit'),
        ];
    }
}
