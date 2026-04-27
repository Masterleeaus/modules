<?php

namespace Modules\Accountings\Filament\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Modules\Accountings\Entities\BasPeriod;
use Modules\Accountings\Services\GstReportService;

class BasSummaryPage extends Page
{
    protected static ?string $slug = 'accounting/bas-summary';

    protected static ?string $navigationLabel = 'BAS Summary';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?int $navigationSort = 40;

    protected string $view = 'accountings::filament.pages.bas-summary';

    public ?string $from = null;
    public ?string $to = null;
    public string $basis = 'accrual';

    public array $summary = [];
    public array $periods = [];

    public function mount(): void
    {
        $now        = now();
        $quarter    = (int) ceil($now->month / 3);
        $this->from = $now->copy()->month(($quarter - 1) * 3 + 1)->startOfMonth()->toDateString();
        $this->to   = $now->copy()->month($quarter * 3)->endOfMonth()->toDateString();
        $this->loadSummary();
        $this->loadPeriods();
    }

    public function filter(): void
    {
        $this->loadSummary();
    }

    protected function loadSummary(): void
    {
        $service       = app(GstReportService::class);
        $this->summary = $service->summary($this->from, $this->to, $this->basis);
    }

    protected function loadPeriods(): void
    {
        $orgId = auth()->user()?->organization_id;

        if (! $orgId) {
            $this->periods = [];
            return;
        }

        $this->periods = BasPeriod::where('organization_id', $orgId)
            ->orderByDesc('period_start')
            ->limit(8)
            ->get()
            ->toArray();
    }

    public function savePeriod(): void
    {
        $orgId = auth()->user()?->organization_id;

        if (! $orgId || ! $this->from || ! $this->to) {
            Notification::make()->danger()->title('Missing date range')->send();
            return;
        }

        $service = app(GstReportService::class);
        $data    = $service->summary($this->from, $this->to, $this->basis);

        BasPeriod::updateOrCreate(
            [
                'organization_id' => $orgId,
                'period_start'    => $this->from,
                'period_end'      => $this->to,
            ],
            [
                'period_type'   => 'quarterly',
                'gst_collected' => $data['gst_collected'],
                'gst_paid'      => $data['gst_paid'],
                'net_gst'       => $data['net_gst'],
                'status'        => 'draft',
            ]
        );

        $this->loadPeriods();

        Notification::make()->success()->title('BAS period saved')->send();
    }

    protected function getViewData(): array
    {
        return [
            'summary' => $this->summary,
            'periods' => $this->periods,
            'from'    => $this->from,
            'to'      => $this->to,
            'basis'   => $this->basis,
        ];
    }
}
