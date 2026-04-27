<?php

namespace Modules\GroundZero\Filament\Widgets;

use App\Models\DriverLocation;
use App\Models\Job;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DispatchStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $orgId = auth()->user()?->organization_id;

        $activeTechs = User::role('technician')
            ->where('organization_id', $orgId)
            ->whereHas('driverLocations', fn ($q) => $q->where('recorded_at', '>=', now()->subMinutes(30)))
            ->count();

        $jobsEnRoute = Job::where('organization_id', $orgId)
            ->where('status', Job::STATUS_EN_ROUTE)
            ->count();

        $jobsInProgress = Job::where('organization_id', $orgId)
            ->where('status', Job::STATUS_IN_PROGRESS)
            ->count();

        $jobsScheduledToday = Job::where('organization_id', $orgId)
            ->where('status', Job::STATUS_SCHEDULED)
            ->whereDate('scheduled_at', today())
            ->count();

        return [
            Stat::make('Active Technicians', $activeTechs)
                ->description('GPS ping in last 30 min')
                ->icon('heroicon-o-user-group')
                ->color('success'),

            Stat::make('En Route', $jobsEnRoute)
                ->description('Jobs currently en route')
                ->icon('heroicon-o-truck')
                ->color('warning'),

            Stat::make('In Progress', $jobsInProgress)
                ->description('Jobs currently in progress')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('info'),

            Stat::make('Scheduled Today', $jobsScheduledToday)
                ->description('Remaining jobs for today')
                ->icon('heroicon-o-calendar')
                ->color('primary'),
        ];
    }
}
