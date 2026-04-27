<?php

namespace Modules\Accountings\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PayablesService
{
    public function summary(int $days = 14): array
    {
        $companyId = $this->currentCompanyId();
        $today = date('Y-m-d');
        $end = date('Y-m-d', strtotime("+$days days"));

        $dueSoon = $this->sumBetween($today, $end, $companyId);
        return [
            'due_soon_total' => $dueSoon['total'],
            'due_soon_count' => $dueSoon['count'],
        ];
    }

    public function list(string $bucket = 'due_14', int $limit = 50): array
    {
        if (!Schema::hasTable('expenses') || !Schema::hasColumn('expenses','amount')) {
            return ['rows' => [], 'bucket' => $bucket, 'note' => 'Expenses table not available.'];
        }

        $companyId = $this->currentCompanyId();
        $dateCol = $this->detectExpenseDateColumn();
        if (!$dateCol) return ['rows'=>[], 'bucket'=>$bucket, 'note'=>'Expense date columns not found.'];

        $today = date('Y-m-d');
        $start = $today;
        $end = date('Y-m-d', strtotime('+14 days'));
        if ($bucket === 'due_30') $end = date('Y-m-d', strtotime('+30 days'));

        $q = DB::table('expenses')->select(['expenses.id', DB::raw("expenses.$dateCol as date_due"), 'expenses.amount']);
        if (Schema::hasColumn('expenses','item_name')) $q->addSelect('expenses.item_name');
        if (Schema::hasColumn('expenses','description')) $q->addSelect('expenses.description');

        $vj = $this->vendorJoinInfo();
        if ($vj) {
            [$fk,$table,$nameCol] = $vj;
            if (Schema::hasColumn('expenses', $fk)) {
                $q->addSelect("expenses.$fk");
                $q->leftJoin($table, "$table.id", '=', "expenses.$fk");
                $q->addSelect(DB::raw("$table.$nameCol as vendor_name"));
            }
        }

        $q->whereBetween(DB::raw("expenses.$dateCol"), [$start, $end]);
        if ($companyId && Schema::hasColumn('expenses','company_id')) $q->where('expenses.company_id',$companyId);
        if (Schema::hasColumn('expenses','status')) $q->whereNotIn('expenses.status',['cancelled','canceled','void']);
        if (Schema::hasColumn('expenses','is_paid')) $q->where('expenses.is_paid',0);
        elseif (Schema::hasColumn('expenses','payment_status')) $q->whereNotIn('expenses.payment_status',['paid','complete','completed']);

        $rows = $q->orderBy(DB::raw("expenses.$dateCol"),'asc')->limit($limit)->get();

        $out = [];
        foreach ($rows as $e) {
            $label = null;
            if (property_exists($e,'vendor_name') && $e->vendor_name) $label = (string)$e->vendor_name;
            elseif (property_exists($e,'item_name') && $e->item_name) $label = (string)$e->item_name;
            elseif (property_exists($e,'description') && $e->description) $label = (string)$e->description;
            else $label = 'Expense #' . $e->id;

            $out[] = [
                'id' => (int)$e->id,
                'date' => (string)$e->date_due,
                'amount' => (float)$e->amount,
                'label' => $label,
            ];
        }

        return ['rows'=>$out,'bucket'=>$bucket,'note'=>null];
    }

    private function sumBetween(?string $from, ?string $to, ?int $companyId): array
    {
        if (!Schema::hasTable('expenses') || !Schema::hasColumn('expenses','amount')) return ['total'=>0.0,'count'=>0];
        $dateCol = $this->detectExpenseDateColumn();
        if (!$dateCol) return ['total'=>0.0,'count'=>0];

        $q = DB::table('expenses')->select(['id','amount',$dateCol])->whereNotNull($dateCol);
        if ($from && $to) $q->whereBetween($dateCol, [$from, $to]);
        if ($companyId && Schema::hasColumn('expenses','company_id')) $q->where('company_id',$companyId);
        if (Schema::hasColumn('expenses','status')) $q->whereNotIn('status',['cancelled','canceled','void']);
        if (Schema::hasColumn('expenses','is_paid')) $q->where('is_paid',0);
        elseif (Schema::hasColumn('expenses','payment_status')) $q->whereNotIn('payment_status',['paid','complete','completed']);

        $rows=$q->get();
        $total=0.0; $count=0;
        foreach ($rows as $r) { $total += (float)$r->amount; $count++; }
        return ['total'=>$total,'count'=>$count];
    }

    private function detectExpenseDateColumn(): ?string
    {
        if (Schema::hasColumn('expenses','expense_date')) return 'expense_date';
        if (Schema::hasColumn('expenses','bill_date')) return 'bill_date';
        if (Schema::hasColumn('expenses','date')) return 'date';
        return null;
    }

    private function vendorJoinInfo(): ?array
    {
        $candidates = [
            ['vendor_id','vendors','name'],
            ['supplier_id','suppliers','name'],
            ['vendor_id','vendor','name'],
            ['supplier_id','supplier','name'],
        ];
        foreach ($candidates as [$fk,$t,$nameCol]) {
            if (!Schema::hasTable($t)) continue;
            if (!Schema::hasColumn($t,'id')) continue;
            if (!Schema::hasColumn($t,$nameCol)) continue;
            return [$fk,$t,$nameCol];
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
