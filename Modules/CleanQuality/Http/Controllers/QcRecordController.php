<?php

namespace Modules\CleanQuality\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Modules\CleanQuality\Entities\InspectionTemplate;
use Modules\CleanQuality\Entities\QcRecord;
use Modules\CleanQuality\Entities\QcRecordItem;
use Modules\CleanQuality\Support\ModuleAccess;
use Illuminate\Support\Facades\Event;

class QcRecordController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('quality_control::sidebar.qc_records');
        $this->middleware(function ($request, $next) {
            abort_403(!ModuleAccess::userHasModule($this->user));

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        abort_403(!in_array(ModuleAccess::permissionLevel('view_quality_control'), ['all']));

        $companyId = $this->user->company_id ?? null;

        $records = QcRecord::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->with(['cleaner', 'template'])
            ->latest()
            ->paginate(20);

        $this->records = $records;

        return view('quality_control::qc-records.index', $this->data);
    }

    public function create()
    {
        abort_403(!in_array(ModuleAccess::permissionLevel('add_quality_control'), ['all']));

        $this->templates = InspectionTemplate::where('is_active', true)->orderBy('name')->get();
        $this->statuses  = QcRecord::STATUSES;

        return view('quality_control::qc-records.create', $this->data);
    }

    public function store(Request $request)
    {
        abort_403(!in_array(ModuleAccess::permissionLevel('add_quality_control'), ['all']));

        $validated = $request->validate([
            'booking_id'   => 'nullable|string|max:36',
            'cleaner_id'   => 'nullable|integer|min:1',
            'template_id'  => 'nullable|integer|min:1',
            'schedule_id'  => 'nullable|integer|min:1',
            'notes'        => 'nullable|string',
            'inspected_at' => 'nullable|date',
            'items'        => 'nullable|array',
            'items.*.item_label' => 'required|string|max:191',
            'items.*.score'      => 'required|integer|min:0|max:100',
            'items.*.weight'     => 'nullable|integer|min:0|max:100',
            'items.*.notes'      => 'nullable|string',
        ]);

        $record = QcRecord::create([
            'company_id'   => $this->user->company_id ?? null,
            'booking_id'   => $validated['booking_id'] ?? null,
            'cleaner_id'   => $validated['cleaner_id'] ?? null,
            'template_id'  => $validated['template_id'] ?? null,
            'schedule_id'  => $validated['schedule_id'] ?? null,
            'status'       => 'pending',
            'overall_score'=> 0,
            'notes'        => $validated['notes'] ?? null,
            'inspected_by' => $this->user->id,
            'inspected_at' => $validated['inspected_at'] ?? now(),
        ]);

        foreach ($validated['items'] ?? [] as $item) {
            QcRecordItem::create([
                'company_id' => $record->company_id,
                'record_id'  => $record->id,
                'item_label' => $item['item_label'],
                'score'      => (int) $item['score'],
                'weight'     => (int) ($item['weight'] ?? 0),
                'notes'      => $item['notes'] ?? null,
            ]);
        }

        $record->recalculateScore();

        $this->maybeAutoTriggerReclean($record);

        return redirect()
            ->route('qc-records.show', $record->id)
            ->with('success', __('quality_control::messages.qc_record_created'));
    }

    public function show($id)
    {
        abort_403(!in_array(ModuleAccess::permissionLevel('view_quality_control'), ['all']));

        $record = QcRecord::with(['items', 'cleaner', 'template', 'inspector'])->findOrFail($id);

        $this->record           = $record;
        $this->canTriggerReclean = in_array(ModuleAccess::permissionLevel('trigger_reclean'), ['all'])
            && !$record->reclean_triggered
            && $record->isBelowThreshold();

        return view('quality_control::qc-records.show', $this->data);
    }

    public function destroy($id)
    {
        abort_403(!in_array(ModuleAccess::permissionLevel('delete_quality_control'), ['all']));

        $record = QcRecord::findOrFail($id);
        $record->delete();

        return redirect()
            ->route('qc-records.index')
            ->with('success', __('quality_control::messages.qc_record_deleted'));
    }

    /**
     * POST /account/qc-records/{id}/trigger-reclean
     */
    public function triggerReclean(Request $request, $id)
    {
        abort_403(!in_array(ModuleAccess::permissionLevel('trigger_reclean'), ['all']));

        $record = QcRecord::findOrFail($id);

        if ($record->reclean_triggered) {
            return back()->with('warning', __('quality_control::messages.reclean_already_triggered'));
        }

        $record->reclean_triggered    = true;
        $record->reclean_triggered_at = now();
        $record->status               = 'reclean_required';
        $record->save();

        // Dispatch a loose-coupled event that other modules can listen to.
        Event::dispatch('quality_control.reclean_triggered', [$record]);

        if (config('quality_control.create_issue_on_needs_reclean', true)) {
            Event::dispatch('inspection.needs_reclean', [$record->booking_id, $record->cleaner_id, $record->id]);
        }

        return back()->with('success', __('quality_control::messages.reclean_triggered'));
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    private function maybeAutoTriggerReclean(QcRecord $record): void
    {
        if (!$record->isBelowThreshold()) {
            return;
        }

        $record->status = 'reclean_required';
        $record->save();

        Event::dispatch('quality_control.reclean_triggered', [$record]);

        if (config('quality_control.create_issue_on_needs_reclean', true)) {
            Event::dispatch('inspection.needs_reclean', [$record->booking_id, $record->cleaner_id, $record->id]);
        }
    }
}
