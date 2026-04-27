<?php

namespace Modules\ZeroPay\Filament\Pages;

use Filament\Pages\Page;

class MarginAnalysisPage extends Page
{
    protected static ?string $slug = 'zero-pay/margin-analysis';

    protected static ?string $navigationLabel = 'Margin Analysis';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string|\UnitEnum|null $navigationGroup = 'Cashflow';

    protected static ?int $navigationSort = 30;

    protected string $view = 'zero_pay::filament.pages.margin-analysis';
}
