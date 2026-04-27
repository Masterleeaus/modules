<?php

namespace Modules\Accountings\Services;

use Illuminate\Support\Facades\DB;

/**
 * Best-effort revenue resolver.
 *
 * Host apps differ wildly. We attempt to find revenue grouped by a "job_ref" key.
 *
 * Strategies:
 *  1) invoices.job_ref exists -> use it
 *  2) invoices.job_id exists -> attempt join to jobs/work_orders/projects tables to get a ref column
 *  3) fall back to casting job_id as string
 *
 * Total column detection: prefers total/grand_total/amount, then sums line totals if invoice_items exists.
 */
class InvoiceRevenueResolver
{
    public function revenueByJobRef(?string $from, ?string $to, bool $paidOnly = false): array
    {
        if (!DB::getSchemaBuilder()->hasTable('invoices')) {
            return [];
        }

        $user = auth()->user();
        $companyId = $user->company_id ?? null;
        $userId = $user->id ?? null;

        $inv = DB::table('invoices');

        if ($companyId && DB::getSchemaBuilder()->hasColumn('invoices', 'company_id')) {
            $inv->where('company_id', $companyId);
        }
        if ($userId && DB::getSchemaBuilder()->hasColumn('invoices', 'user_id')) {
            $inv->where('user_id', $userId);
        }

        // date filter
        $dateCol = null;
        foreach (['issue_date','invoice_date','paid_at','payment_date','created_at'] as $c) {
            if (DB::getSchemaBuilder()->hasColumn('invoices', $c)) { $dateCol = $c; break; }
        }
        if ($from && $dateCol) $inv->whereDate($dateCol, '>=', $from);
        if ($to && $dateCol) $inv->whereDate($dateCol, '<=', $to);

        if ($paidOnly) {
            // best-effort paid status
            foreach (['status','payment_status'] as $c) {
                if (DB::getSchemaBuilder()->hasColumn('invoices', $c)) {
                    $inv->whereIn($c, ['paid','Paid','PAID']);
                    break;
                }
            }
            foreach (['paid_at','paid_date'] as $c) {
                if (DB::getSchemaBuilder()->hasColumn('invoices', $c)) {
                    $inv->whereNotNull($c);
                    break;
                }
            }
        }

        // job ref expression
        $jobRefExpr = null;
        if (DB::getSchemaBuilder()->hasColumn('invoices', 'job_ref')) {
            $jobRefExpr = 'job_ref';
        } elseif (DB::getSchemaBuilder()->hasColumn('invoices', 'job_id')) {
            // Try join to jobs/work_orders/projects
            $jobRefExpr = 'CAST(invoices.job_id as CHAR)';
        } elseif (DB::getSchemaBuilder()->hasColumn('invoices', 'project_id')) {
            $jobRefExpr = 'CAST(invoices.project_id as CHAR)';
        } elseif (DB::getSchemaBuilder()->hasColumn('invoices', 'work_order_id')) {
            $jobRefExpr = 'CAST(invoices.work_order_id as CHAR)';
        } else {
            // cannot map
            return [];
        }

        // total column
        $totalCol = null;
        foreach (['grand_total','total','total_amount','amount','final_amount'] as $c) {
            if (DB::getSchemaBuilder()->hasColumn('invoices', $c)) { $totalCol = $c; break; }
        }

        if ($totalCol) {
            $rows = $inv->selectRaw("{$jobRefExpr} as job_ref, SUM({$totalCol}) as revenue")
                ->whereRaw("{$jobRefExpr} is not null")
                ->groupBy('job_ref')
                ->get();
            $out = [];
            foreach ($rows as $r) {
                $jr = trim((string)$r->job_ref);
                if ($jr === '') continue;
                $out[$jr] = round((float)$r->revenue, 2);
            }
            return $out;
        }

        // fallback: sum invoice items if table exists
        foreach (['invoice_items','invoice_item','invoicelines','invoice_lines'] as $itemsTable) {
            if (!DB::getSchemaBuilder()->hasTable($itemsTable)) continue;

            // detect foreign key
            $fk = null;
            foreach (['invoice_id','invoices_id'] as $c) {
                if (DB::getSchemaBuilder()->hasColumn($itemsTable, $c)) { $fk = $c; break; }
            }
            if (!$fk) continue;

            $itemTotalCol = null;
            foreach (['total','line_total','amount','price'] as $c) {
                if (DB::getSchemaBuilder()->hasColumn($itemsTable, $c)) { $itemTotalCol = $c; break; }
            }
            if (!$itemTotalCol) continue;

            $rows = $inv
                ->join($itemsTable, "{$itemsTable}.{$fk}", '=', 'invoices.id')
                ->selectRaw("{$jobRefExpr} as job_ref, SUM({$itemsTable}.{$itemTotalCol}) as revenue")
                ->groupBy('job_ref')
                ->get();

            $out = [];
            foreach ($rows as $r) {
                $jr = trim((string)$r->job_ref);
                if ($jr === '') continue;
                $out[$jr] = round((float)$r->revenue, 2);
            }
            return $out;
        }

        return [];
    }
}
