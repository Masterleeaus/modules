<?php

namespace Modules\ZeroPay\Filament\Pages;

use Filament\Pages\Page;

class BASHelperPage extends Page
{
    protected static ?string $slug = 'zero-pay/bas-helper';

    protected static ?string $navigationLabel = 'BAS Helper';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Cashflow';

    protected static ?int $navigationSort = 20;

    protected string $view = 'zero_pay::filament.pages.bas-helper';
}
