<?php

namespace Modules\CleanQuality\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AccountBaseController;
use Modules\CleanQuality\Entities\QcRecord;
use Modules\CleanQuality\Support\ModuleAccess;
use Illuminate\Support\Facades\Event;

class RecleanController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('quality_control::sidebar.reclean_management');
        $this->middleware(function ($request, $next) {
            abort_403(!ModuleAccess::userHasModule($this->user));

            return $next($request);
        });
    }

    /**
     * List all QC records that require or have had a re-clean.
     */
    public function index(Request $request)
    {
        abort_403(!in_array(ModuleAccess::permissionLevel('trigger_reclean'), ['all']));

        $companyId = $this->user->company_id ?? null;

        $records = collect();

        if (DB::getSchemaBuilder()->hasTable('qc_records')) {
            $records = QcRecord::query()
                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                ->whereIn('status', ['reclean_required', 'reclean_done'])
                ->with(['cleaner', 'template'])
                ->latest()
                ->paginate(20);
        }

        $this->records = $records;

        return view('quality_control::reclean.index', $this->data);
    }

    /**
     * Mark a re-clean as completed.
     */
    public function markDone(Request $request, int $id)
    {
        abort_403(!in_array(ModuleAccess::permissionLevel('trigger_reclean'), ['all']));

        $record = QcRecord::findOrFail($id);

        $record->status = 'reclean_done';
        $record->save();

        Event::dispatch('quality_control.reclean_done', [$record]);

        return back()->with('success', __('quality_control::messages.reclean_marked_done'));
    }
}
