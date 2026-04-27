<?php

namespace Modules\Accountings\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CashRunwayService
{
    public function runway(int $days = 14): array
    {
        $today = date('Y-m-d');
        $end = date('Y-m-d', strtotime("+$days days"));

        $companyId = $this->currentCompanyId();

        $ar = $this->receivablesBetween($today, $end, $companyId);
        $ap = $this->payablesBetween($today, $end, $companyId);

        return [
            'from' => $today,
            'to' => $end,
            'days' => $days,
            'ar' => $ar,
            'ap' => $ap,
            'net' => $ar - $ap,
        ];
    }

    private function receivablesBetween(string $from, string $to, ?int $companyId): float
    {
        if (!Schema::hasTable('invoices')) return 0.0;
        if (!Schema::hasColumn('invoices','total')) return 0.0;

        $dateCol = Schema::hasColumn('invoices','due_date') ? 'due_date'
            : (Schema::hasColumn('invoices','issue_date') ? 'issue_date'
            : (Schema::hasColumn('invoices','invoice_date') ? 'invoice_date'
            : (Schema::hasColumn('invoices','date') ? 'date' : null)));
        if (!$dateCol) return 0.0;

        $q = DB::table('invoices')
            ->whereBetween($dateCol, [$from, $to]);

        if ($companyId && Schema::hasColumn('invoices','company_id')) $q->where('company_id', $companyId);
        if (Schema::hasColumn('invoices','status')) {
            $q->whereNotIn('status', ['paid','cancelled','canceled','void', 4, 5]);
        }

        $total = (float) $q->sum('total');

        // subtract payments if possible
        $paymentInfo = $this->detectInvoicePaymentsTable();
        if (!$paymentInfo) return $total;

        [$pt,$invCol,$amtCol] = $paymentInfo;
        $paid = (float) DB::table($pt)
            ->join('invoices', "invoices.id", "=", "$pt.$invCol")
            ->whereBetween("invoices.$dateCol", [$from, $to])
            ->when($companyId && Schema::hasColumn('invoices','company_id'), fn($qq) => $qq->where('invoices.company_id', $companyId))
            ->sum("$pt.$amtCol");

        return max(0, $total - $paid);
    }

    private function payablesBetween(string $from, string $to, ?int $companyId): float
    {
        if (!Schema::hasTable('expenses')) return 0.0;
        if (!Schema::hasColumn('expenses','amount')) return 0.0;

        $dateCol = Schema::hasColumn('expenses','expense_date') ? 'expense_date'
            : (Schema::hasColumn('expenses','date') ? 'date'
            : (Schema::hasColumn('expenses','bill_date') ? 'bill_date' : null));
        if (!$dateCol) return 0.0;

        $q = DB::table('expenses')->whereBetween($dateCol, [$from, $to]);
        if ($companyId && Schema::hasColumn('expenses','company_id')) $q->where('company_id', $companyId);

        return (float) $q->sum('amount');
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
        try {
            if (function_exists('company') && company()) return (int) company()->id;
        } catch (\Throwable $e) {}
        try {
            $u = auth()->user();
            if ($u && isset($u->company_id)) return (int)$u->company_id;
        } catch (\Throwable $e) {}
        try {
            $sid = session('company_id');
            if ($sid) return (int)$sid;
        } catch (\Throwable $e) {}
        return null;
    }
}
