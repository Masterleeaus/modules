<?php

namespace Modules\ZeroPay\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OverdueRiskWidget extends BaseWidget
{
    protected static ?int $sort = 30;

    protected function getStats(): array
    {
        $orgId = auth()->user()?->organization_id;

        $query = Invoice::where('organization_id', $orgId)
            ->where('status', Invoice::STATUS_OVERDUE)
            ->where('due_at', '<', now());

        $totalOverdue  = $query->sum('balance_due');
        $overdueCount  = $query->count();

        return [
            Stat::make('Overdue', '$' . number_format((float) $totalOverdue, 2))
                ->description("{$overdueCount} overdue invoice(s)")
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
