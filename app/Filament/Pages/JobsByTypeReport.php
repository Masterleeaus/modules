<?php

namespace App\Filament\Pages;

use App\Models\Job;
use App\Models\JobType;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class JobsByTypeReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Jobs by Type';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.jobs-by-type-report';

    public string $from = '';
    public string $to   = '';

    public array $rows     = [];
    public array $statuses = [];

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

        $rows = Job::where('organization_id', $orgId)
            ->whereBetween('scheduled_at', [$from, $to])
            ->select('job_type_id', DB::raw('count(*) as total'), 'status')
            ->groupBy('job_type_id', 'status')
            ->with('jobType:id,name,color')
            ->get();

        $byType = [];
        foreach ($rows as $row) {
            $key = $row->job_type_id ?? 0;
            if (! isset($byType[$key])) {
                $byType[$key] = [
                    'type'     => $row->jobType
                        ? ['id' => $row->jobType->id, 'name' => $row->jobType->name, 'color' => $row->jobType->color]
                        : ['id' => 0, 'name' => 'Untyped', 'color' => '#94a3b8'],
                    'statuses' => [],
                    'total'    => 0,
                ];
            }
            $byType[$key]['statuses'][$row->status] = $row->total;
            $byType[$key]['total'] += $row->total;
        }

        usort($byType, fn ($a, $b) => $b['total'] <=> $a['total']);

        $this->rows     = array_values($byType);
        $this->statuses = Job::statuses();
    }
}
