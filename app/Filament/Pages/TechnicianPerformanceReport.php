<?php

namespace App\Filament\Pages;

use App\Models\Job;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class TechnicianPerformanceReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Technician Performance';

    protected static ?int $navigationSort = 30;

    protected string $view = 'filament.pages.technician-performance-report';

    public string $from = '';
    public string $to   = '';

    public array $technicians = [];

    public function mount(): void
    {
        $this->from = Carbon::now()->subDays(29)->toDateString();
        $this->to   = Carbon::now()->toDateString();
        $this->loadReport();
    }

    public function filter(): void
    {
        $this->loadReport();
    }

    private function loadReport(): void
    {
        $orgId = auth()->user()?->organization_id;
        $from  = Carbon::parse($this->from)->startOfDay();
        $to    = Carbon::parse($this->to)->endOfDay();

        $this->technicians = User::where('organization_id', $orgId)
            ->role('technician')
            ->with(['jobs' => function ($q) use ($orgId, $from, $to) {
                $q->where('organization_id', $orgId)
                  ->where('status', Job::STATUS_COMPLETED)
                  ->whereBetween('completed_at', [$from, $to])
                  ->with('invoice');
            }])
            ->get()
            ->map(function (User $tech) {
                $jobs      = $tech->jobs;
                $completed = $jobs->count();
                $revenue   = $jobs->sum(fn ($j) => $j->invoice?->total ?? 0);

                $durations = $jobs
                    ->filter(fn ($j) => $j->started_at && $j->completed_at)
                    ->map(fn ($j) => $j->completed_at->diffInMinutes($j->started_at));

                $avgDuration = $durations->count() > 0 ? round($durations->avg()) : null;

                return [
                    'id'                   => $tech->id,
                    'name'                 => $tech->name,
                    'jobs_completed'       => $completed,
                    'revenue'              => (float) $revenue,
                    'avg_duration_minutes' => $avgDuration,
                ];
            })
            ->sortByDesc('jobs_completed')
            ->values()
            ->all();
    }
}
