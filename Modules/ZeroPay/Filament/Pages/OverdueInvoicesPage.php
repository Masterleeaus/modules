<?php

namespace Modules\ZeroPay\Filament\Pages;

use Filament\Pages\Page;

class OverdueInvoicesPage extends Page
{
    protected static ?string $slug = 'zero-pay/overdue-invoices';

    protected static ?string $navigationLabel = 'Overdue Invoices';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string|\UnitEnum|null $navigationGroup = 'Collections';

    protected static ?int $navigationSort = 10;

    protected string $view = 'zero_pay::filament.pages.overdue-invoices';
}
