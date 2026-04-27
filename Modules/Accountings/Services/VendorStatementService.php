<?php

namespace Modules\Accountings\Services;

use Illuminate\Support\Facades\DB;

class VendorStatementService
{
    public function summary(?string $from, ?string $to): array
    {
        $user = auth()->user();
        $companyId = $user->company_id ?? null;
        $userId = $user->id ?? null;

        $schema = DB::getSchemaBuilder();

        $bills = DB::table('acc_bills as b')
            ->leftJoin('acc_vendors as v', 'v.id', '=', 'b.vendor_id')
            ->selectRaw('b.vendor_id, COALESCE(v.name, "(No Vendor)") as vendor_name, SUM(b.total) as billed_total')
            ->when($companyId, fn($q) => $q->where('b.company_id', $companyId))
            ->when($userId, fn($q) => $q->where('b.user_id', $userId));

        if ($from) $bills->whereDate('b.bill_date', '>=', $from);
        if ($to) $bills->whereDate('b.bill_date', '<=', $to);

        $bills = $bills->groupBy('b.vendor_id', 'vendor_name')->get();

        $paymentsMap = [];
        if ($schema->hasTable('acc_bill_payments')) {
            $pay = DB::table('acc_bill_payments as p')
                ->join('acc_bills as b', 'b.id', '=', 'p.bill_id')
                ->selectRaw('b.vendor_id as vendor_id, SUM(p.amount) as paid_total')
                ->when($companyId, fn($q) => $q->where('p.company_id', $companyId))
                ->when($userId, fn($q) => $q->where('p.user_id', $userId));

            if ($from) $pay->whereDate('p.paid_at', '>=', $from);
            if ($to) $pay->whereDate('p.paid_at', '<=', $to);

            foreach ($pay->groupBy('b.vendor_id')->get() as $r) {
                $paymentsMap[(string)$r->vendor_id] = (float)$r->paid_total;
            }
        }

        $rows = [];
        foreach ($bills as $b) {
            $vendorId = (string)$b->vendor_id;
            $billed = round((float)$b->billed_total, 2);
            $paid = round((float)($paymentsMap[$vendorId] ?? 0), 2);
            $rows[] = [
                'vendor_id' => $b->vendor_id,
                'vendor_name' => $b->vendor_name,
                'billed_total' => $billed,
                'paid_total' => $paid,
                'balance' => round($billed - $paid, 2),
            ];
        }

        // sort by balance desc
        usort($rows, fn($a,$c) => ($c['balance'] <=> $a['balance']));

        return $rows;
    }
}
