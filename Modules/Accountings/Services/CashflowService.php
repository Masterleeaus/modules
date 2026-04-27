<?php

namespace Modules\Accountings\Services;

use Modules\Accountings\Entities\Journald;
use Illuminate\Support\Collection;

class CashflowService
{
    /**
     * Builds a month series for cash accounts based on tagged COA (is_cash_account=1).
     * Falls back to description heuristic only if no accounts are tagged.
     */
    public function monthlySeries(?string $from = null, ?string $to = null): array
    {
        $q = Journald::query()->with(['coa','journal']);

        if ($from) $q->whereHas('journal', fn($jq) => $jq->whereDate('journal_date','>=',$from));
        if ($to) $q->whereHas('journal', fn($jq) => $jq->whereDate('journal_date','<=',$to));

        $rows = $q->get();

        $hasTagged = $rows->contains(function ($d) {
            return (bool) optional($d->coa)->is_cash_account;
        });

        $series = [];
        foreach ($rows as $d) {
            $date = optional($d->journal)->journal_date;
            if (!$date) continue;
            $month = date('Y-m', strtotime($date));

            $coa = $d->coa;
            $desc = strtolower((string) optional($coa)->coa_desc);
            $isCash = $hasTagged
                ? (bool) optional($coa)->is_cash_account
                : (str_contains($desc,'cash') || str_contains($desc,'bank'));

            if (!$isCash) continue;

            $debit = (float)($d->debit ?? 0);
            $credit = (float)($d->credit ?? 0);

            $series[$month] = $series[$month] ?? ['inflow'=>0,'outflow'=>0,'net'=>0];
            // for cash accounts: debits increase cash (inflow), credits decrease cash (outflow)
            $series[$month]['inflow'] += $debit;
            $series[$month]['outflow'] += $credit;
        }

        ksort($series);
        foreach ($series as $m => $v) {
            $series[$m]['net'] = $v['inflow'] - $v['outflow'];
        }

        return $series;
    }
}
