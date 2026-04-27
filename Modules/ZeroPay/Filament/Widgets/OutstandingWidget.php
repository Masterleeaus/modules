<?php

namespace Modules\ZeroPay\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OutstandingWidget extends BaseWidget
{
    protected static ?int $sort = 20;

    protected function getStats(): array
    {
        $orgId = auth()->user()?->organization_id;

        $query = Invoice::where('organization_id', $orgId)
            ->whereNotIn('status', [Invoice::STATUS_PAID, Invoice::STATUS_VOID]);

        $totalOutstanding = $query->sum('balance_due');
        $invoiceCount     = $query->count();

        return [
            Stat::make('Outstanding', '$' . number_format((float) $totalOutstanding, 2))
                ->description("{$invoiceCount} unpaid invoice(s)")
                ->icon('heroicon-o-receipt-percent')
                ->color('warning'),
        ];
    }
}
