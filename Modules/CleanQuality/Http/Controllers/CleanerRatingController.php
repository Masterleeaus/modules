<?php

namespace Modules\CleanQuality\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AccountBaseController;
use Modules\CleanQuality\Entities\QcRecord;
use Modules\CleanQuality\Support\ModuleAccess;

class CleanerRatingController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('quality_control::sidebar.cleaner_ratings');
        $this->middleware(function ($request, $next) {
            abort_403(!ModuleAccess::userHasModule($this->user));

            return $next($request);
        });
    }

    /**
     * Display cleaner ratings aggregated from all QC records.
     */
    public function index(Request $request)
    {
        abort_403(!in_array(ModuleAccess::permissionLevel('view_cleaner_ratings'), ['all']));

        $companyId = $this->user->company_id ?? null;

        $ratings = collect();

        if (DB::getSchemaBuilder()->hasTable('qc_records')) {
            $ratings = QcRecord::query()
                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                ->whereNotNull('cleaner_id')
                ->whereNotNull('inspected_at')
                ->select(
                    'cleaner_id',
                    DB::raw('ROUND(AVG(overall_score), 1) as avg_score'),
                    DB::raw('COUNT(*) as total_records'),
                    DB::raw('SUM(CASE WHEN status = "pass" THEN 1 ELSE 0 END) as pass_count'),
                    DB::raw('SUM(CASE WHEN status IN ("fail","reclean_required") THEN 1 ELSE 0 END) as fail_count'),
                    DB::raw('MAX(inspected_at) as last_inspected_at')
                )
                ->groupBy('cleaner_id')
                ->orderByDesc('avg_score')
                ->get();
        }

        // Eager-load cleaner user objects (loose coupling — ignore if User model doesn't exist).
        if ($ratings->isNotEmpty() && class_exists(\App\Models\User::class)) {
            $cleanerIds = $ratings->pluck('cleaner_id')->unique()->filter();
            $cleaners   = \App\Models\User::whereIn('id', $cleanerIds)
                ->select('id', 'name', 'email', 'image')
                ->get()
                ->keyBy('id');

            $ratings = $ratings->map(function ($row) use ($cleaners) {
                $row->cleaner = $cleaners->get($row->cleaner_id);

                return $row;
            });
        }

        $this->ratings = $ratings;

        return view('quality_control::cleaner-ratings.index', $this->data);
    }

    /**
     * Show QC trend for a single cleaner.
     */
    public function show(Request $request, int $cleanerId)
    {
        abort_403(!in_array(ModuleAccess::permissionLevel('view_cleaner_ratings'), ['all']));

        $companyId = $this->user->company_id ?? null;

        $records = collect();

        if (DB::getSchemaBuilder()->hasTable('qc_records')) {
            $records = QcRecord::query()
                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                ->where('cleaner_id', $cleanerId)
                ->with('template')
                ->orderBy('inspected_at')
                ->get();
        }

        $this->records   = $records;
        $this->cleanerId = $cleanerId;
        $this->cleaner   = class_exists(\App\Models\User::class)
            ? \App\Models\User::find($cleanerId)
            : null;

        return view('quality_control::cleaner-ratings.show', $this->data);
    }
}
