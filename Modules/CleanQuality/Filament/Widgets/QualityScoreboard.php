<?php

namespace Modules\CleanQuality\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Modules\CleanQuality\Entities\Inspection;
use Modules\CleanQuality\Entities\QcRecord;
use Modules\CleanQuality\Support\Enums\InspectionStatus;

class QualityScoreboard extends Widget
{
    protected static ?string $heading = 'Quality Scoreboard';
    protected static ?int $sort = 10;
    protected string $view = 'clean_quality::filament.widgets.quality-scoreboard';

    protected function getViewData(): array
    {
        $since = now()->subDays(30);

        // Both Inspection (HasCompany → CompanyScope) and QcRecord (CompanyScoped trait) carry
        // global scopes that restrict every query to the authenticated user's company_id.
        // No explicit ->where('company_id', ...) is needed here.
        $inspections = Inspection::query()
            ->where('created_at', '>=', $since)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as passed,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as reclean_booked,
                AVG(CASE WHEN score IS NOT NULL THEN score END) as avg_score
            ', [
                InspectionStatus::PASSED,
                InspectionStatus::FAILED,
                InspectionStatus::RECLEAN_BOOKED,
            ])
            ->first();

        $qcRecords = QcRecord::query()
            ->where('created_at', '>=', $since)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as passed,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN reclean_triggered = 1 THEN 1 ELSE 0 END) as reclean_triggered,
                AVG(overall_score) as avg_score
            ', ['pass', 'fail'])
            ->first();

        $inspTotal  = (int) ($inspections?->total ?? 0);
        $inspPassed = (int) ($inspections?->passed ?? 0);
        $inspRate   = $this->passRate($inspPassed, $inspTotal);

        $qcTotal  = (int) ($qcRecords?->total ?? 0);
        $qcPassed = (int) ($qcRecords?->passed ?? 0);
        $qcRate   = $this->passRate($qcPassed, $qcTotal);

        return [
            'period'            => '30 days',
            'insp_total'        => $inspTotal,
            'insp_passed'       => $inspPassed,
            'insp_failed'       => (int) ($inspections?->failed ?? 0),
            'insp_reclean'      => (int) ($inspections?->reclean_booked ?? 0),
            'insp_pass_rate'    => $inspRate,
            'insp_avg_score'    => $inspections?->avg_score !== null ? round((float) $inspections->avg_score, 1) : null,
            'qc_total'          => $qcTotal,
            'qc_passed'         => $qcPassed,
            'qc_failed'         => (int) ($qcRecords?->failed ?? 0),
            'qc_reclean'        => (int) ($qcRecords?->reclean_triggered ?? 0),
            'qc_pass_rate'      => $qcRate,
            'qc_avg_score'      => $qcRecords?->avg_score !== null ? round((float) $qcRecords->avg_score, 1) : null,
        ];
    }

    private function passRate(int $passed, int $total): ?int
    {
        return $total > 0 ? (int) round($passed / $total * 100) : null;
    }
}

