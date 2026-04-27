<?php

namespace Modules\Accountings\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Modules\Accountings\Services\JobProfitabilityService;

class ProfitabilityPage extends Page
{
    protected static ?string $slug = 'accounting/profitability';

    protected static ?string $navigationLabel = 'Job Profitability';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?int $navigationSort = 30;

    protected string $view = 'accountings::filament.pages.profitability';

    public ?string $from = null;
    public ?string $to = null;
    public bool $paidOnly = false;

    public array $rows = [];

    public function mount(): void
    {
        $this->from = now()->startOfMonth()->toDateString();
        $this->to   = now()->endOfMonth()->toDateString();
        $this->loadData();
    }

    public function filter(): void
    {
        $this->loadData();
    }

    protected function loadData(): void
    {
        $service    = app(JobProfitabilityService::class);
        $this->rows = $service->summary($this->from, $this->to, true, $this->paidOnly);
    }

    protected function getViewData(): array
    {
        return [
            'rows'     => $this->rows,
            'from'     => $this->from,
            'to'       => $this->to,
            'paidOnly' => $this->paidOnly,
        ];
    }
}
