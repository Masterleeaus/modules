<?php

namespace Modules\CleanQuality\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AccountBaseController;
use Modules\CleanQuality\Entities\QcRecord;
use Modules\CleanQuality\Support\ModuleAccess;

class QcDashboardController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('quality_control::sidebar.qc_dashboard');
        $this->middleware(function ($request, $next) {
            abort_403(!ModuleAccess::userHasModule($this->user));

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        abort_403(!in_array(ModuleAccess::permissionLevel('view_quality_control'), ['all']));

        $canViewReports  = in_array(ModuleAccess::permissionLevel('view_qc_reports'), ['all']);
        $canViewRatings  = in_array(ModuleAccess::permissionLevel('view_cleaner_ratings'), ['all']);

        // Aggregate statistics (safe fallback when table doesn't exist yet).
        $stats = [];

        if (DB::getSchemaBuilder()->hasTable('qc_records')) {
            $companyId = $this->user->company_id ?? null;

            $base = QcRecord::query();

            if ($companyId) {
                $base->where('company_id', $companyId);
            }

            $stats['total']       = (clone $base)->count();
            $stats['pass']        = (clone $base)->where('status', 'pass')->count();
            $stats['fail']        = (clone $base)->where('status', 'fail')->count();
            $stats['reclean']     = (clone $base)->where('status', 'reclean_required')->count();
            $stats['avg_score']   = round((clone $base)->avg('overall_score') ?? 0, 1);
            $stats['fail_rate']   = $stats['total'] > 0
                ? round(($stats['fail'] / $stats['total']) * 100, 1)
                : 0;

            // Top 5 cleaners by average score (last 90 days).
            $stats['top_performers'] = $canViewRatings
                ? (clone $base)
                    ->where('inspected_at', '>=', now()->subDays(90))
                    ->whereNotNull('cleaner_id')
                    ->select('cleaner_id', DB::raw('ROUND(AVG(overall_score),1) as avg_score'), DB::raw('COUNT(*) as total'))
                    ->groupBy('cleaner_id')
                    ->orderByDesc('avg_score')
                    ->limit(5)
                    ->get()
                : collect();

            // Bottom 5 cleaners.
            $stats['bottom_performers'] = $canViewRatings
                ? (clone $base)
                    ->where('inspected_at', '>=', now()->subDays(90))
                    ->whereNotNull('cleaner_id')
                    ->select('cleaner_id', DB::raw('ROUND(AVG(overall_score),1) as avg_score'), DB::raw('COUNT(*) as total'))
                    ->groupBy('cleaner_id')
                    ->orderBy('avg_score')
                    ->limit(5)
                    ->get()
                : collect();

            // Recent 10 records.
            $stats['recent'] = (clone $base)
                ->with(['cleaner', 'template'])
                ->latest()
                ->limit(10)
                ->get();
        }

        $this->stats           = $stats;
        $this->canViewReports  = $canViewReports;
        $this->canViewRatings  = $canViewRatings;

        return view('quality_control::dashboard.index', $this->data);
    }
}
