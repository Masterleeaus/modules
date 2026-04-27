<?php

namespace Modules\Accountings\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReceivablesService
{
    public function summary(int $days = 14): array
    {
        $companyId = $this->currentCompanyId();
        $today = date('Y-m-d');
        $end = date('Y-m-d', strtotime("+$days days"));

        $overdue = $this->outstandingBetween(null, date('Y-m-d', strtotime('-1 day')), $companyId);
        $dueSoon = $this->outstandingBetween($today, $end, $companyId);

        return [
            'overdue_total' => $overdue['total'],
            'overdue_count' => $overdue['count'],
            'due_soon_total' => $dueSoon['total'],
            'due_soon_count' => $dueSoon['count'],
        ];
    }

    /**
     * Returns invoice rows with (best-effort) client name and current balance.
     * $bucket: overdue | due_14 | due_30 | all
     */
    public function list(string $bucket = 'overdue', int $limit = 50): array
    {
        if (!Schema::hasTable('invoices') || !Schema::hasColumn('invoices','total')) {
            return ['rows' => [], 'bucket' => $bucket, 'note' => 'Invoices table not available.'];
        }

        $companyId = $this->currentCompanyId();
        $dateCol = $this->detectInvoiceDateColumn();
        if (!$dateCol) return ['rows'=>[], 'bucket'=>$bucket, 'note'=>'Invoice date columns not found.'];

        $today = date('Y-m-d');
        $start = null; $end = null;

        if ($bucket === 'overdue') $end = date('Y-m-d', strtotime('-1 day'));
        elseif ($bucket === 'due_14') { $start = $today; $end = date('Y-m-d', strtotime('+14 days')); }
        elseif ($bucket === 'due_30') { $start = $today; $end = date('Y-m-d', strtotime('+30 days')); }
        // all = no date filter

        $paidByInvoice = $this->paidByInvoice();

        $q = DB::table('invoices')->select(['invoices.id','invoices.total', DB::raw("invoices.$dateCol as date_due")]);

        $hasClientId = Schema::hasColumn('invoices','client_id');
        if ($hasClientId) $q->addSelect('invoices.client_id');

        if (Schema::hasColumn('invoices','invoice_number')) $q->addSelect('invoices.invoice_number');
        elseif (Schema::hasColumn('invoices','invoice_no')) $q->addSelect(DB::raw('invoices.invoice_no as invoice_number'));

        if ($hasClientId) {
            $cj = $this->clientJoinInfo();
            if ($cj) {
                [$table,$nameCol] = $cj;
                $q->leftJoin($table, "$table.id", '=', 'invoices.client_id');
                $q->addSelect(DB::raw("$table.$nameCol as client_name"));
            }
        }

        if ($companyId && Schema::hasColumn('invoices','company_id')) $q->where('invoices.company_id',$companyId);
        if (Schema::hasColumn('invoices','status')) $q->whereNotIn('invoices.status',['paid','cancelled','canceled','void',4,5]);

        if ($start && $end) $q->whereBetween(DB::raw("invoices.$dateCol"), [$start, $end]);
        elseif ($end) $q->where(DB::raw("invoices.$dateCol"), '<=', $end);

        $rows = $q->orderBy(DB::raw("invoices.$dateCol"),'asc')->limit($limit)->get();

        $out = [];
        foreach ($rows as $inv) {
            $id = (int)$inv->id;
            $bal = max(0, (float)$inv->total - (float)($paidByInvoice[$id] ?? 0));
            if ($bal <= 0) continue;

            $invNo = (property_exists($inv,'invoice_number') && $inv->invoice_number) ? (string)$inv->invoice_number : ('INV-' . $id);
            $clientName = (property_exists($inv,'client_name') && $inv->client_name) ? (string)$inv->client_name : null;

            $out[] = [
                'id' => $id,
                'invoice_number' => $invNo,
                'date' => (string)$inv->date_due,
                'balance' => $bal,
                'client_id' => $hasClientId && property_exists($inv,'client_id') ? (int)$inv->client_id : null,
                'client_name' => $clientName,
            ];
        }

        return ['rows'=>$out,'bucket'=>$bucket,'note'=>null];
    }

    /**
     * A/R Aging buckets + top client totals (overdue only).
     */
    public function aging(int $limitInvoices = 500): array
    {
        $data = $this->list('overdue', $limitInvoices);
        $rows = $data['rows'];

        $today = new \DateTimeImmutable(date('Y-m-d'));
        $buckets = [
            'overdue_0_7' => ['label'=>'0–7 days overdue', 'total'=>0.0, 'count'=>0],
            'overdue_8_30' => ['label'=>'8–30 days overdue', 'total'=>0.0, 'count'=>0],
            'overdue_31_60' => ['label'=>'31–60 days overdue', 'total'=>0.0, 'count'=>0],
            'overdue_60_plus' => ['label'=>'60+ days overdue', 'total'=>0.0, 'count'=>0],
        ];

        $clientTotals = [];
        foreach ($rows as $r) {
            $due = null;
            try { $due = new \DateTimeImmutable(substr((string)$r['date'],0,10)); } catch (\Throwable $e) { $due = null; }
            $days = 0;
            if ($due) {
                $diff = $due->diff($today);
                $days = (int)$diff->days;
            }

            $bal = (float)$r['balance'];
            if ($days <= 7) $k='overdue_0_7';
            elseif ($days <= 30) $k='overdue_8_30';
            elseif ($days <= 60) $k='overdue_31_60';
            else $k='overdue_60_plus';

            $buckets[$k]['total'] += $bal;
            $buckets[$k]['count']++;

            $ck = ($r['client_id'] !== null) ? ('id:' . $r['client_id']) : ('name:' . ($r['client_name'] ?? 'Unknown'));
            if (!isset($clientTotals[$ck])) {
                $clientTotals[$ck] = [
                    'client_id' => $r['client_id'],
                    'client_name' => $r['client_name'] ?? '—',
                    'total' => 0.0,
                ];
            }
            $clientTotals[$ck]['total'] += $bal;
        }

        $clientTotals = array_values($clientTotals);
        usort($clientTotals, function($a,$b){ return ($b['total'] <=> $a['total']); });
        $clientTotals = array_slice($clientTotals, 0, 25);

        return [
            'buckets' => $buckets,
            'clients' => $clientTotals,
            'rows' => $rows,
            'note' => $data['note'],
        ];
    }

    public function topOverdue(int $limit = 20): array
    {
        $data = $this->list('overdue', 500);
        $rows = $data['rows'];
        usort($rows, function($a,$b){ return ((float)$b['balance'] <=> (float)$a['balance']); });
        $rows = array_slice($rows, 0, $limit);
        return ['rows'=>$rows, 'note'=>$data['note']];
    }

    private function outstandingBetween(?string $from, ?string $to, ?int $companyId): array
    {
        if (!Schema::hasTable('invoices') || !Schema::hasColumn('invoices','total')) return ['total'=>0.0,'count'=>0];

        $dateCol = $this->detectInvoiceDateColumn();
        if (!$dateCol) return ['total'=>0.0,'count'=>0];

        $paidByInvoice = $this->paidByInvoice();

        $q = DB::table('invoices')->select(['id','total',$dateCol])->whereNotNull($dateCol);
        if ($companyId && Schema::hasColumn('invoices','company_id')) $q->where('company_id',$companyId);
        if (Schema::hasColumn('invoices','status')) $q->whereNotIn('status',['paid','cancelled','canceled','void',4,5]);

        if ($from && $to) $q->whereBetween($dateCol, [$from, $to]);
        elseif ($to) $q->where($dateCol, '<=', $to);
        elseif ($from) $q->where($dateCol, '>=', $from);

        $rows = $q->get();
        $total = 0.0; $count = 0;
        foreach ($rows as $inv) {
            $id=(int)$inv->id;
            $bal = max(0, (float)$inv->total - (float)($paidByInvoice[$id] ?? 0));
            if ($bal<=0) continue;
            $total += $bal; $count++;
        }
        return ['total'=>$total,'count'=>$count];
    }

    private function paidByInvoice(): array
    {
        $paymentInfo = $this->detectInvoicePaymentsTable();
        if (!$paymentInfo) return [];

        [$pt,$invCol,$amtCol] = $paymentInfo;
        $rows = DB::table($pt)->selectRaw("$invCol as invoice_id, SUM($amtCol) as paid_amt")->groupBy('invoice_id')->get();
        $map=[];
        foreach ($rows as $r) $map[(int)$r->invoice_id]=(float)$r->paid_amt;
        return $map;
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

    private function clientJoinInfo(): ?array
    {
        $candidates = [
            ['clients', 'name'],
            ['clients', 'company_name'],
            ['client_details', 'company_name'],
            ['users', 'name'],
        ];
        foreach ($candidates as [$t,$nameCol]) {
            if (!Schema::hasTable($t)) continue;
            if (!Schema::hasColumn($t,'id')) continue;
            if (!Schema::hasColumn($t,$nameCol)) continue;
            return [$t,$nameCol];
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
