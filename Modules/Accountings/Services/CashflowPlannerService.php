<?php

namespace Modules\Accountings\Services;

use Modules\Accountings\Entities\CashflowBudget;
use Modules\Accountings\Entities\RecurringExpense;

/**
 * Weekly planner for tradies.
 * Adds:
 * - GST buffer toggle (adds 10% to outflows as safety buffer)
 * - Warning flags (materials-heavy weeks, cash negative)
 */
class CashflowPlannerService
{
    public function plan(int $weeks = 8, string $scenario = 'expected', float $startingCash = 0.0, bool $gstBuffer = false): array
    {
        $scenario = in_array($scenario, ['expected','best','worst'], true) ? $scenario : 'expected';

        $mult = $this->scenarioMultipliers($scenario);
        $weekly = (new CashRunwayWeeklyService())->weekly($weeks);

        $budgetWeekly = $this->budgetWeekly($weeks);
        $recurringWeekly = $this->recurringWeekly($weekly['from'], $weeks);

        $rows = [];
        $cash = $startingCash;

        foreach ($weekly['rows'] as $r) {
            $wk = $r['week_start'];

            $arBase = (float)$r['ar'];
            $apBase = (float)$r['ap'];

            $budgetIn = (float)($budgetWeekly[$wk]['in'] ?? 0);
            $budgetOut = (float)($budgetWeekly[$wk]['out'] ?? 0);

            $recOut = (float)($recurringWeekly[$wk] ?? 0);

            $in = ($arBase + $budgetIn) * $mult['in'];
            $outBase = ($apBase + $budgetOut + $recOut) * $mult['out'];

            $gst = $gstBuffer ? ($outBase * 0.10) : 0.0;
            $out = $outBase + $gst;

            $net = $in - $out;
            $cash = $cash + $net;

            $den = max(1.0, ($apBase + $budgetOut + $recOut));
            $materialsHeavy = ($apBase > 0) && (($apBase / $den) >= 0.60);
            $cashRisk = ($cash < 0);

            $rows[] = [
                'week_start' => $r['week_start'],
                'week_end' => $r['week_end'],
                'in_ar' => $arBase,
                'in_budget' => $budgetIn,
                'out_ap' => $apBase,
                'out_budget' => $budgetOut,
                'out_recurring' => $recOut,
                'out_gst_buffer' => $gst,
                'in_total' => $in,
                'out_total' => $out,
                'net' => $net,
                'cash_end' => $cash,
                'warn_materials_heavy' => $materialsHeavy,
                'warn_cash_negative' => $cashRisk,
            ];
        }

        return [
            'scenario' => $scenario,
            'gst_buffer' => $gstBuffer,
            'weeks' => $weeks,
            'from' => $weekly['from'],
            'to' => $weekly['to'],
            'starting_cash' => $startingCash,
            'rows' => $rows,
            'totals' => [
                'in_total' => array_sum(array_map(fn($r)=>$r['in_total'], $rows)),
                'out_total' => array_sum(array_map(fn($r)=>$r['out_total'], $rows)),
                'net' => array_sum(array_map(fn($r)=>$r['net'], $rows)),
                'cash_end' => $cash,
            ],
        ];
    }

    private function scenarioMultipliers(string $scenario): array
    {
        if ($scenario === 'best') return ['in' => 1.10, 'out' => 0.90];
        if ($scenario === 'worst') return ['in' => 0.85, 'out' => 1.10];
        return ['in' => 1.00, 'out' => 1.00];
    }

    private function budgetWeekly(int $weeks): array
    {
        $budget = null;
        try { $budget = CashflowBudget::where('is_active', 1)->latest('id')->first(); } catch (\Throwable $e) { $budget = null; }
        if (!$budget) return [];

        $in = (float)$budget->expected_monthly_inflow;
        $out = (float)$budget->expected_monthly_outflow;

        $perWeekIn = $in / 4.345;
        $perWeekOut = $out / 4.345;

        $start = (new CashRunwayWeeklyService())->weekly(max(1,$weeks))['from'];
        $startDt = new \DateTimeImmutable($start);

        $map = [];
        $cursor = $startDt;
        for ($i=0; $i<$weeks; $i++) {
            $wk = $cursor->format('Y-m-d');
            $map[$wk] = ['in' => $perWeekIn, 'out' => $perWeekOut];
            $cursor = $cursor->modify('+7 days');
        }
        return $map;
    }

    private function recurringWeekly(string $fromYmd, int $weeks): array
    {
        if (!class_exists(RecurringExpense::class)) return [];
        try { $recurrings = RecurringExpense::where('is_active', 1)->get(); } catch (\Throwable $e) { return []; }

        $start = new \DateTimeImmutable($fromYmd);
        $end = $start->modify('+' . ($weeks * 7) . ' days');

        $map = [];
        foreach ($recurrings as $r) {
            $amt = (float)$r->amount;
            $freq = (string)$r->frequency;
            $cursor = $start;

            while ($cursor < $end) {
                $wk = (new \DateTimeImmutable($cursor->format('Y-m-d')))->modify('-' . ((int)$cursor->format('N')-1) . ' days')->format('Y-m-d');
                $map[$wk] = ($map[$wk] ?? 0) + $this->recurringToWeeklyAmount($amt, $freq);
                $cursor = $cursor->modify('+7 days');
            }
        }

        ksort($map);
        return $map;
    }

    private function recurringToWeeklyAmount(float $amt, string $freq): float
    {
        switch ($freq) {
            case 'weekly': return $amt;
            case 'monthly': return $amt / 4.345;
            case 'quarterly': return $amt / (4.345 * 3);
            case 'yearly': return $amt / (4.345 * 12);
            default: return $amt / 4.345;
        }
    }
}
