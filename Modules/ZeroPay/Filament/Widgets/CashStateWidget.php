<?php

namespace Modules\ZeroPay\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CashStateWidget extends BaseWidget
{
    protected static ?int $sort = 10;

    protected function getStats(): array
    {
        $orgId = auth()->user()?->organization_id;

        $collected = Payment::where('organization_id', $orgId)
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        return [
            Stat::make('Cash Collected This Month', '$' . number_format((float) $collected, 2))
                ->description('Total payments received')
                ->icon('heroicon-o-banknotes')
                ->color('success'),
        ];
    }
}
