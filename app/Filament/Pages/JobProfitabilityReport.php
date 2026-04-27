<?php

namespace App\Filament\Pages;

use App\Models\Job;
use App\Models\JobType;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class JobProfitabilityReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Job Profitability';

    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.pages.job-profitability-report';

    public string  $from        = '';
    public string  $to          = '';
    public ?int    $jobTypeId   = null;
    public ?int    $technicianId = null;

    public array $jobs        = [];
    public array $jobTypes    = [];
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

        $query = Job::where('organization_id', $orgId)
            ->where('status', Job::STATUS_COMPLETED)
            ->whereBetween('completed_at', [$from, $to])
            ->with(['jobType:id,name,color', 'assignedTechnician:id,name', 'invoice', 'lineItems']);

        if ($this->jobTypeId) {
            $query->where('job_type_id', $this->jobTypeId);
        }
        if ($this->technicianId) {
            $query->where('assigned_to', $this->technicianId);
        }

        $this->jobs = $query->get()->map(function (Job $job) {
            $revenue  = $job->invoice?->total ?? 0;
            $parts    = $job->lineItems->sum(fn ($li) => $li->unit_price * $li->quantity);
            $margin   = $revenue - $parts;
            $marginPct = $revenue > 0 ? round($margin / $revenue * 100, 1) : null;

            return [
                'id'           => $job->id,
                'title'        => $job->title,
                'completed_at' => $job->completed_at?->toDateString(),
                'job_type'     => $job->jobType ? ['id' => $job->jobType->id, 'name' => $job->jobType->name, 'color' => $job->jobType->color] : null,
                'technician'   => $job->assignedTechnician ? ['id' => $job->assignedTechnician->id, 'name' => $job->assignedTechnician->name] : null,
                'revenue'      => (float) $revenue,
                'parts_cost'   => (float) $parts,
                'margin'       => (float) $margin,
                'margin_pct'   => $marginPct,
            ];
        })->all();

        $this->jobTypes    = JobType::where('organization_id', $orgId)->orderBy('name')->get(['id', 'name'])->all();
        $this->technicians = User::where('organization_id', $orgId)->role('technician')->orderBy('name')->get(['id', 'name'])->all();
    }
}
