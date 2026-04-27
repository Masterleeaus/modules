<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Job;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class JobStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $orgId     = auth()->user()?->organization_id;
        $today     = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd   = Carbon::now()->endOfWeek();

        $jobsToday = Job::where('organization_id', $orgId)
            ->whereDate('scheduled_at', $today)
            ->whereNotIn('status', [Job::STATUS_CANCELLED])
            ->count();

        $revenueThisWeek = Invoice::where('organization_id', $orgId)
            ->where('status', Invoice::STATUS_PAID)
            ->whereBetween('paid_at', [$weekStart, $weekEnd])
            ->sum('total');

        $ar = Invoice::where('organization_id', $orgId)
            ->whereIn('status', [Invoice::STATUS_SENT, Invoice::STATUS_PARTIAL, Invoice::STATUS_OVERDUE])
            ->sum('balance_due');

        $overdueInvoices = Invoice::where('organization_id', $orgId)
            ->where('status', Invoice::STATUS_OVERDUE)
            ->count();

        $openJobs = Job::where('organization_id', $orgId)
            ->whereNotIn('status', [Job::STATUS_COMPLETED, Job::STATUS_CANCELLED])
            ->count();

        $unassignedJobs = Job::where('organization_id', $orgId)
            ->whereNull('assigned_to')
            ->whereNotIn('status', [Job::STATUS_COMPLETED, Job::STATUS_CANCELLED])
            ->count();

        return [
            Stat::make('Jobs Today', $jobsToday)
                ->description('Scheduled for today')
                ->color('info')
                ->icon('heroicon-o-calendar-days'),

            Stat::make('Revenue This Week', '$'.number_format((float) $revenueThisWeek, 2))
                ->description('Paid invoices this week')
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Accounts Receivable', '$'.number_format((float) $ar, 2))
                ->description('Outstanding balance due')
                ->color('warning')
                ->icon('heroicon-o-document-currency-dollar'),

            Stat::make('Overdue Invoices', $overdueInvoices)
                ->description('Invoices past due date')
                ->color($overdueInvoices > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-exclamation-triangle'),

            Stat::make('Open Jobs', $openJobs)
                ->description('Not yet completed or cancelled')
                ->color('primary')
                ->icon('heroicon-o-wrench-screwdriver'),

            Stat::make('Unassigned Jobs', $unassignedJobs)
                ->description('Need a technician assigned')
                ->color($unassignedJobs > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-user-plus'),
        ];
    }
}
