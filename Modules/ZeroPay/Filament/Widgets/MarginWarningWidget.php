<?php

namespace Modules\ZeroPay\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MarginWarningWidget extends BaseWidget
{
    protected static ?int $sort = 50;

    /** Margin threshold below which a job is considered at risk (percent). */
    protected const THRESHOLD = 20;

    protected function getStats(): array
    {
        return [
            Stat::make('Low-Margin Jobs', '—')
                ->description('Margin analysis requires cost data')
                ->icon('heroicon-o-arrow-trending-down')
                ->color('gray'),
        ];
    }
}
