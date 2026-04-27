<?php

namespace App\Filament\Pages;

use App\Models\Job;
use App\Models\JobReview;
use App\Models\RecurringJobTemplate;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class CleaningOps extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|\UnitEnum|null $navigationGroup = 'Dispatch';

    protected static ?string $navigationLabel = 'Cleaning Ops';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.cleaning-ops';

    public array $stats           = [];
    public array $activeJobs      = [];
    public array $overdueTemplates = [];

    public function mount(): void
    {
        $this->loadData();
    }

    public function refresh(): void
    {
        $this->loadData();
    }

    private function loadData(): void
    {
        $orgId = auth()->user()?->organization_id;
        $today = Carbon::today();

        $todayJobs = Job::where('organization_id', $orgId)
            ->whereDate('scheduled_at', $today)
            ->whereNotIn('status', [Job::STATUS_CANCELLED])
            ->get();

        $completedToday = $todayJobs->where('status', Job::STATUS_COMPLETED)->count();
        $scheduledToday = $todayJobs->whereIn('status', [Job::STATUS_SCHEDULED, Job::STATUS_EN_ROUTE, Job::STATUS_IN_PROGRESS])->count();

        // Average rating this week
        $avgRating = JobReview::whereHas('job', fn ($q) =>
            $q->where('organization_id', $orgId)
              ->where('created_at', '>=', Carbon::now()->startOfWeek())
        )->avg('rating');

        $this->stats = [
            'scheduled_today' => $scheduledToday,
            'completed_today' => $completedToday,
            'avg_rating'      => $avgRating ? round($avgRating, 1) : null,
            'unassigned'      => Job::where('organization_id', $orgId)
                ->whereNull('assigned_to')
                ->whereIn('status', [Job::STATUS_SCHEDULED])
                ->count(),
        ];

        // Active jobs with technician info
        $this->activeJobs = Job::where('organization_id', $orgId)
            ->whereIn('status', [Job::STATUS_EN_ROUTE, Job::STATUS_IN_PROGRESS])
            ->with(['assignedTechnician', 'customer', 'property'])
            ->orderBy('scheduled_at')
            ->get()
            ->map(fn (Job $j) => [
                'id'         => $j->id,
                'title'      => $j->title,
                'status'     => $j->status,
                'technician' => $j->assignedTechnician?->name ?? 'Unassigned',
                'customer'   => $j->customer?->full_name,
                'address'    => $j->property?->full_address,
            ])
            ->all();

        // Overdue recurring templates (last_generated_on more than 1 frequency-period ago)
        $this->overdueTemplates = RecurringJobTemplate::where('organization_id', $orgId)
            ->where('is_active', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('last_generated_on')
                  ->orWhere('last_generated_on', '<', $today->copy()->subWeeks(2));
            })
            ->with('customer')
            ->limit(10)
            ->get()
            ->map(fn (RecurringJobTemplate $t) => [
                'id'       => $t->id,
                'title'    => $t->title,
                'customer' => $t->customer?->full_name,
                'frequency' => RecurringJobTemplate::frequencies()[$t->frequency] ?? $t->frequency,
                'last_run' => $t->last_generated_on?->toDateString() ?? 'Never',
            ])
            ->all();
    }
}
