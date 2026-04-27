<?php

namespace Modules\ZeroPay\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentPipelineWidget extends BaseWidget
{
    protected static ?int $sort = 40;

    protected function getStats(): array
    {
        $orgId = auth()->user()?->organization_id;

        $expectedIn7Days = Invoice::where('organization_id', $orgId)
            ->whereIn('status', [Invoice::STATUS_SENT, Invoice::STATUS_PARTIAL])
            ->whereBetween('due_at', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
            ->sum('balance_due');

        return [
            Stat::make('Expected (7 days)', '$' . number_format((float) $expectedIn7Days, 2))
                ->description('Payments due in next 7 days')
                ->icon('heroicon-o-calendar-days')
                ->color('info'),
        ];
    }
}
