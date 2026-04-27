<?php

namespace Modules\ZeroPay\Filament\Pages;

use Filament\Pages\Page;

class XeroSyncPage extends Page
{
    protected static ?string $slug = 'zero-pay/xero-sync';

    protected static ?string $navigationLabel = 'Xero Sync';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting Sync';

    protected static ?int $navigationSort = 10;

    protected string $view = 'zero_pay::filament.pages.xero-sync';
}
