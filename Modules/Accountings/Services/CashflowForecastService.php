<?php

namespace Modules\Accountings\Services;

use Modules\Accountings\Entities\RecurringExpense;
use Modules\Accountings\Entities\CashflowBudget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CashflowForecastService
{
    public function forecast(int $months = 3): array
    {
        $budget = CashflowBudget::where('is_active', 1)->latest('id')->first();
        $expectedIn = $budget ? (float)$budget->expected_monthly_inflow : 0.0;
        $expectedOut = $budget ? (float)$budget->expected_monthly_outflow : 0.0;

        $recurrings = RecurringExpense::where('is_active', 1)->get();
        $recurringMonthly = 0.0;

        foreach ($recurrings as $r) {
            $amt = (float)$r->amount;
            switch ($r->frequency) {
                case 'weekly': $recurringMonthly += ($amt * 52 / 12); break;
                case 'quarterly': $recurringMonthly += ($amt / 3); break;
                case 'yearly': $recurringMonthly += ($amt / 12); break;
                default: $recurringMonthly += $amt; break; // monthly
            }
        }

        $arByMonth = $this->receivablesOutstandingByMonth();
        $apByMonth = $this->payablesByMonth();

        $forecast = [];
        $dt = new \DateTime(date('Y-m-01'));
        for ($i=0; $i<$months; $i++) {
            $m = $dt->format('Y-m');

            $ar = (float)($arByMonth[$m] ?? 0);
            $ap = (float)($apByMonth[$m] ?? 0);

            $inflow = $expectedIn + $ar;
            $outflow = $expectedOut + $recurringMonthly + $ap;

            $forecast[$m] = [
                'expected_inflow' => $inflow,
                'expected_outflow' => $outflow,
                'net' => $inflow - $outflow,
                'ar' => $ar,
                'ap' => $ap,
            ];
            $dt->modify('+1 month');
        }

        return [
            'budget' => $budget ? [
                'name' => $budget->name,
                'expected_monthly_inflow' => $expectedIn,
                'expected_monthly_outflow' => $expectedOut,
            ] : null,
            'recurring_monthly_outflow' => $recurringMonthly,
            'months' => $forecast,
        ];
    }

    private function receivablesOutstandingByMonth(): array
    {
        if (!Schema::hasTable('invoices')) return [];
        if (!Schema::hasColumn('invoices','total')) return [];

        $companyId = $this->currentCompanyId();
        $dateCol = $this->detectInvoiceDateColumn();
        if (!$dateCol) return [];

        $paidByInvoice = [];
        $paymentInfo = $this->detectInvoicePaymentsTable();
        if ($paymentInfo) {
            [$pt,$invCol,$amtCol] = $paymentInfo;
            $payRows = DB::table($pt)
                ->selectRaw("$invCol as invoice_id, SUM($amtCol) as paid_amt")
                ->groupBy('invoice_id')
                ->get();
            foreach ($payRows as $r) {
                $paidByInvoice[(int)$r->invoice_id] = (float)$r->paid_amt;
            }
        }

        $invQ = DB::table('invoices')->select(['id','total',$dateCol])->whereNotNull($dateCol);
        if ($companyId && Schema::hasColumn('invoices','company_id')) $invQ->where('company_id',$companyId);

        if (Schema::hasColumn('invoices','status')) {
            $invQ->whereNotIn('status', ['paid','cancelled','canceled','void',4,5]);
        }

        $rows = $invQ->get();
        $out=[];
        foreach ($rows as $inv) {
            $id=(int)$inv->id;
            $total=(float)$inv->total;
            $paid=(float)($paidByInvoice[$id] ?? 0);
            $bal=max(0,$total-$paid);
            if ($bal<=0) continue;

            $ym = date('Y-m', strtotime((string)$inv->{$dateCol}));
            $out[$ym]=($out[$ym] ?? 0)+$bal;
        }
        ksort($out);
        return $out;
    }

    private function payablesByMonth(): array
    {
        if (!Schema::hasTable('expenses')) return [];
        if (!Schema::hasColumn('expenses','amount')) return [];

        $companyId = $this->currentCompanyId();
        $dateCol = $this->detectExpenseDateColumn();
        if (!$dateCol) return [];

        $q = DB::table('expenses')
            ->selectRaw("DATE_FORMAT($dateCol, '%Y-%m') as ym, SUM(amount) as amt")
            ->whereNotNull($dateCol);

        if ($companyId && Schema::hasColumn('expenses','company_id')) $q->where('company_id',$companyId);

        if (Schema::hasColumn('expenses','status')) {
            $q->whereNotIn('status',['cancelled','canceled','void']);
        }
        if (Schema::hasColumn('expenses','is_paid')) {
            $q->where('is_paid',0);
        } elseif (Schema::hasColumn('expenses','payment_status')) {
            $q->whereNotIn('payment_status',['paid','complete','completed']);
        }

        $rows=$q->groupBy('ym')->get();
        $out=[];
        foreach ($rows as $r) $out[$r->ym]=(float)$r->amt;
        ksort($out);
        return $out;
    }

    private function detectInvoiceDateColumn(): ?string
    {
        if (Schema::hasColumn('invoices','due_date')) return 'due_date';
        if (Schema::hasColumn('invoices','invoice_date')) return 'invoice_date';
        if (Schema::hasColumn('invoices','issue_date')) return 'issue_date';
        if (Schema::hasColumn('invoices','date')) return 'date';
        return null;
    }

    private function detectExpenseDateColumn(): ?string
    {
        if (Schema::hasColumn('expenses','expense_date')) return 'expense_date';
        if (Schema::hasColumn('expenses','bill_date')) return 'bill_date';
        if (Schema::hasColumn('expenses','date')) return 'date';
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
            if (!Schema::hasColumn($t, $invCol)) continue;
            if (!Schema::hasColumn($t, $amtCol)) continue;
            return [$t,$invCol,$amtCol];
        }
        return null;
    }

    private function currentCompanyId(): ?int
    {
        try { if (function_exists('company') && company()) return (int)company()->id; } catch (\Throwable $e) {}
        try { $u=auth()->user(); if ($u && isset($u->company_id)) return (int)$u->company_id; } catch (\Throwable $e) {}
        try { $sid=session('company_id'); if ($sid) return (int)$sid; } catch (\Throwable $e) {}
        return null;
    }
}
