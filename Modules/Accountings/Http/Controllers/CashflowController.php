<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Accountings\Services\CashflowService;

class CashflowController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');

        $series = (new CashflowService())->monthlySeries($from, $to);

        // KPI snapshot (best-effort) for Titan Zero context + tradie dashboard cards
        $kpis = $this->kpiSnapshot();

        // Overdue invoice list (best-effort, capped) for collections/follow-up intents
        $overdue = $this->topOverdueInvoices(20);

        return view('accountings::cashflow.index', compact('series','from','to','kpis','overdue'));
    }

    private function kpiSnapshot(): array
    {
        $today = date('Y-m-d');
        $companyId = $this->currentCompanyId();

        $out = [
            'today' => $today,
            'overdue_total' => 0.0,
            'overdue_count' => 0,
            'next7_inflows' => 0.0,
            'next7_outflows' => 0.0,
        ];

        // Receivables: invoices due in next 7 days + overdue totals
        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices','total')) {
            $dateCol = $this->detectInvoiceDateColumn();
            if ($dateCol) {
                $q = DB::table('invoices')->whereNotNull($dateCol);

                if ($companyId && Schema::hasColumn('invoices','company_id')) $q->where('company_id', $companyId);
                if (Schema::hasColumn('invoices','status')) $q->whereNotIn('status', ['paid','cancelled','canceled','void',4,5]);

                // overdue
                $oq = clone $q;
                $over = $oq->where(DB::raw($dateCol), '<', $today)
                    ->selectRaw('COUNT(*) as c, SUM(total) as s')->first();
                $out['overdue_count'] = (int)($over->c ?? 0);
                $out['overdue_total'] = (float)($over->s ?? 0);

                // next 7 days inflows
                $to7 = date('Y-m-d', strtotime('+7 days'));
                $nq = clone $q;
                $n = $nq->whereBetween(DB::raw($dateCol), [$today, $to7])
                    ->selectRaw('SUM(total) as s')->first();
                $out['next7_inflows'] = (float)($n->s ?? 0);
            }
        }

        // Payables: expenses in next 7 days (unpaid when possible)
        if (Schema::hasTable('expenses') && Schema::hasColumn('expenses','amount')) {
            $dateCol = $this->detectExpenseDateColumn();
            if ($dateCol) {
                $q = DB::table('expenses')->whereNotNull($dateCol);

                if ($companyId && Schema::hasColumn('expenses','company_id')) $q->where('company_id', $companyId);
                if (Schema::hasColumn('expenses','status')) $q->whereNotIn('status', ['cancelled','canceled','void']);
                if (Schema::hasColumn('expenses','is_paid')) $q->where('is_paid', 0);
                elseif (Schema::hasColumn('expenses','payment_status')) $q->whereNotIn('payment_status', ['paid','complete','completed']);

                $to7 = date('Y-m-d', strtotime('+7 days'));
                $n = $q->whereBetween(DB::raw($dateCol), [$today, $to7])->selectRaw('SUM(amount) as s')->first();
                $out['next7_outflows'] = (float)($n->s ?? 0);
            }
        }

        return $out;
    }

    private function topOverdueInvoices(int $limit = 20): array
    {
        $today = date('Y-m-d');
        $companyId = $this->currentCompanyId();

        if (!Schema::hasTable('invoices') || !Schema::hasColumn('invoices','total')) return [];

        $dateCol = $this->detectInvoiceDateColumn();
        if (!$dateCol) return [];

        $q = DB::table('invoices')
            ->select(['id','total', DB::raw("$dateCol as due_date")])
            ->whereNotNull($dateCol)
            ->where(DB::raw($dateCol), '<', $today);

        if ($companyId && Schema::hasColumn('invoices','company_id')) $q->where('company_id', $companyId);
        if (Schema::hasColumn('invoices','status')) $q->whereNotIn('status', ['paid','cancelled','canceled','void',4,5]);

        $rows = $q->orderBy('total','desc')->limit($limit)->get();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'invoice_id' => (int)$r->id,
                'total' => (float)$r->total,
                'due_date' => substr((string)$r->due_date,0,10),
            ];
        }
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

    private function currentCompanyId(): ?int
    {
        try { if (function_exists('company') && company()) return (int)company()->id; } catch (\Throwable $e) {}
        try { $u=auth()->user(); if ($u && isset($u->company_id)) return (int)$u->company_id; } catch (\Throwable $e) {}
        try { $sid=session('company_id'); if ($sid) return (int)$sid; } catch (\Throwable $e) {}
        return null;
    }
}
