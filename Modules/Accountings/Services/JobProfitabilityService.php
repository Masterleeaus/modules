<?php

namespace Modules\Accountings\Services;

use Illuminate\Support\Facades\DB;

class JobProfitabilityService
{
    public function summary(?string $from, ?string $to, bool $includeRevenue = true, bool $paidOnly = false): array
    {
        $user = auth()->user();
        $companyId = $user->company_id ?? null;
        $userId = $user->id ?? null;

        $q = DB::table('acc_job_costs')
            ->selectRaw('job_ref, SUM(amount) as total_cost, COUNT(*) as line_count, MAX(created_at) as last_cost_at')
            ->whereNotNull('job_ref')
            ->where('job_ref', '!=', '')
            ->when($companyId, fn($qq) => $qq->where('company_id', $companyId))
            ->when($userId, fn($qq) => $qq->where('user_id', $userId));

        if ($from) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $q->whereDate('created_at', '<=', $to);
        }

        $rows = $q->groupBy('job_ref')
            ->orderByDesc('last_cost_at')
            ->limit(500)
            ->get();

        $revenueMap = [];
        if ($includeRevenue) {
            $revenueMap = (new InvoiceRevenueResolver())->revenueByJobRef($from, $to, $paidOnly);
        }

        return $rows->map(function ($r) use ($revenueMap) {
            $jobRef = (string) $r->job_ref;
            $cost = round((float)$r->total_cost, 2);
            $rev = isset($revenueMap[$jobRef]) ? round((float)$revenueMap[$jobRef], 2) : null;

            $profit = null;
            $margin = null;
            if ($rev !== null) {
                $profit = round($rev - $cost, 2);
                $margin = $rev > 0 ? round(($profit / $rev) * 100, 2) : null;
            }

            return [
                'job_ref' => $jobRef,
                'total_cost' => $cost,
                'revenue' => $rev,
                'profit' => $profit,
                'margin' => $margin,
                'line_count' => (int)$r->line_count,
                'last_cost_at' => $r->last_cost_at,
            ];
        })->toArray();
    }
}
