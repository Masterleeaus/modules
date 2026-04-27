<?php

namespace Modules\ZeroPay\Filament\Pages;

use Filament\Pages\Page;
use Modules\ZeroPay\Filament\Widgets\CashStateWidget;
use Modules\ZeroPay\Filament\Widgets\MarginWarningWidget;
use Modules\ZeroPay\Filament\Widgets\OutstandingWidget;
use Modules\ZeroPay\Filament\Widgets\OverdueRiskWidget;
use Modules\ZeroPay\Filament\Widgets\PaymentPipelineWidget;

class CashflowDashboardPage extends Page
{
    protected static ?string $slug = 'zero-pay/cashflow';

    protected static ?string $navigationLabel = 'Cashflow Dashboard';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static string|\UnitEnum|null $navigationGroup = 'Cashflow';

    protected static ?int $navigationSort = 0;

    protected string $view = 'zero_pay::filament.pages.cashflow-dashboard';

    protected function getWidgets(): array
    {
        return [
            CashStateWidget::class,
            OutstandingWidget::class,
            OverdueRiskWidget::class,
            PaymentPipelineWidget::class,
            MarginWarningWidget::class,
        ];
    }
}
