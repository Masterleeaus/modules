<?php

namespace Modules\Accountings\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CashRunwayWeeklyService
{
    public function weekly(int $weeks = 8, float $openingCash = 0.0, string $scenario = 'expected'): array
    {
        $start = $this->weekStart(new \DateTimeImmutable('today'));
        $end = $start->modify('+' . ($weeks * 7) . ' days');

        $companyId = $this->currentCompanyId();

        $in = $this->receivablesWeekly($start, $end, $companyId);
        $out = $this->payablesWeekly($start, $end, $companyId);

        $mult = $this->scenarioMultipliers($scenario);

        $weeksOut = [];
        $cash = (float)$openingCash;

        for ($i=0; $i<$weeks; $i++) {
            $wk = $start->modify('+' . ($i*7) . ' days')->format('Y-m-d');

            $inAmt = (float)($in[$wk] ?? 0.0) * $mult['in'];
            $outAmt = (float)($out[$wk] ?? 0.0) * $mult['out'];

            $net = $inAmt - $outAmt;
            $cash = $cash + $net;

            $weeksOut[] = [
                'week_start' => $wk,
                'inflows' => round($inAmt, 2),
                'outflows' => round($outAmt, 2),
                'net' => round($net, 2),
                'closing_cash' => round($cash, 2),
            ];
        }

        return [
            'scenario' => strtolower(trim($scenario)),
            'opening_cash' => round((float)$openingCash, 2),
            'weeks' => $weeksOut,
            'sources' => [
                'receivables' => $this->receivablesSourceNote(),
                'payables' => $this->payablesSourceNote(),
            ],
        ];
    }

    private function scenarioMultipliers(string $scenario): array
    {
        $scenario = strtolower(trim($scenario));
        if ($scenario === 'best') return ['in'=>1.05, 'out'=>0.98];
        if ($scenario === 'worst') return ['in'=>0.85, 'out'=>1.10];
        return ['in'=>1.00, 'out'=>1.00];
    }

    private function receivablesWeekly(\DateTimeImmutable $from, \DateTimeImmutable $to, ?int $companyId): array
    {
        if (!Schema::hasTable('invoices') || !Schema::hasColumn('invoices','total')) return [];

        $dateCol = $this->detectInvoiceDateColumn();
        if (!$dateCol) return [];

        $paidByInvoice = [];
        $paymentInfo = $this->detectInvoicePaymentsTable();
        if ($paymentInfo) {
            [$pt,$invCol,$amtCol] = $paymentInfo;
            $rows = DB::table($pt)->selectRaw("$invCol as invoice_id, SUM($amtCol) as paid_amt")->groupBy('invoice_id')->get();
            foreach ($rows as $r) $paidByInvoice[(int)$r->invoice_id] = (float)$r->paid_amt;
        }

        $q = DB::table('invoices')
            ->select(['invoices.id','invoices.total', DB::raw("invoices.$dateCol as due_dt")])
            ->whereNotNull($dateCol);

        if ($companyId && Schema::hasColumn('invoices','company_id')) $q->where('invoices.company_id', $companyId);
        if (Schema::hasColumn('invoices','status')) $q->whereNotIn('invoices.status', ['paid','cancelled','canceled','void',4,5]);

        $q->whereBetween(DB::raw("invoices.$dateCol"), [$from->format('Y-m-d'), $to->format('Y-m-d')]);

        $rows = $q->get();
        $out = [];

        foreach ($rows as $inv) {
            $id = (int)$inv->id;
            $bal = max(0, (float)$inv->total - (float)($paidByInvoice[$id] ?? 0));
            if ($bal <= 0) continue;

            $dt = new \DateTimeImmutable(substr((string)$inv->due_dt,0,10));
            $wk = $this->weekStart($dt)->format('Y-m-d');
            $out[$wk] = ($out[$wk] ?? 0) + $bal;
        }

        ksort($out);
        return $out;
    }

    private function payablesWeekly(\DateTimeImmutable $from, \DateTimeImmutable $to, ?int $companyId): array
    {
        if (!Schema::hasTable('expenses') || !Schema::hasColumn('expenses','amount')) return [];

        $dateCol = $this->detectExpenseDateColumn();
        if (!$dateCol) return [];

        $q = DB::table('expenses')
            ->select(['id','amount', DB::raw("$dateCol as bill_dt")])
            ->whereNotNull($dateCol);

        if ($companyId && Schema::hasColumn('expenses','company_id')) $q->where('company_id', $companyId);
        if (Schema::hasColumn('expenses','status')) $q->whereNotIn('status', ['cancelled','canceled','void']);
        if (Schema::hasColumn('expenses','is_paid')) $q->where('is_paid', 0);
        elseif (Schema::hasColumn('expenses','payment_status')) $q->whereNotIn('payment_status', ['paid','complete','completed']);

        $q->whereBetween(DB::raw($dateCol), [$from->format('Y-m-d'), $to->format('Y-m-d')]);

        $rows = $q->get();
        $out = [];

        foreach ($rows as $e) {
            $dt = new \DateTimeImmutable(substr((string)$e->bill_dt,0,10));
            $wk = $this->weekStart($dt)->format('Y-m-d');
            $out[$wk] = ($out[$wk] ?? 0) + (float)$e->amount;
        }

        ksort($out);
        return $out;
    }

    private function receivablesSourceNote(): string
    {
        return "Invoices (detected due date) minus payments when a payments table exists.";
    }

    private function payablesSourceNote(): string
    {
        return "Expenses (detected date) filtered to unpaid when possible.";
    }

    private function detectInvoiceDateColumn(): ?string
    {
        if (Schema::hasColumn('invoices','due_date')) return 'due_date';
        if (Schema::hasColumn('invoices','invoice_date')) return 'invoice_date';
        if (Schema::hasColumn('invoices','issue_date')) return 'issue_date';
        if (Schema::hasColumn('invoices','date')) return 'date';
        return null;
    }

    private function detectInvoicePaymentsTable(): ?array
    {
        $candidates = [
            ['invoice_payments', 'invoice_id', 'amount'],
            ['invoice_payment', 'invoice_id', 'amount'],
            ['payments', 'invoice_id', 'amount'],
            ['payment', 'invoice_id', 'amount'],
        ];
        foreach ($candidates as [$t,$invCol,$amtCol]) {
            if (!Schema::hasTable($t)) continue;
            if (!Schema::hasColumn($t,$invCol)) continue;
            if (!Schema::hasColumn($t,$amtCol)) continue;
            return [$t,$invCol,$amtCol];
        }
        return null;
    }

    private function detectExpenseDateColumn(): ?string
    {
        if (Schema::hasColumn('expenses','expense_date')) return 'expense_date';
        if (Schema::hasColumn('expenses','bill_date')) return 'bill_date';
        if (Schema::hasColumn('expenses','date')) return 'date';
        return null;
    }

    private function weekStart(\DateTimeImmutable $dt): \DateTimeImmutable
    {
        $dow = (int)$dt->format('N'); // 1..7
        return $dt->modify('-' . ($dow-1) . ' days')->setTime(0,0,0);
    }

    private function currentCompanyId(): ?int
    {
        try { if (function_exists('company') && company()) return (int)company()->id; } catch (\Throwable $e) {}
        try { $u=auth()->user(); if ($u && isset($u->company_id)) return (int)$u->company_id; } catch (\Throwable $e) {}
        try { $sid=session('company_id'); if ($sid) return (int)$sid; } catch (\Throwable $e) {}
        return null;
    }
}
