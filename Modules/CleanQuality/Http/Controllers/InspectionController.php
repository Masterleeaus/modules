<?php

namespace Modules\CleanQuality\Http\Controllers;

use App\Helper\Reply;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Modules\CleanQuality\Entities\Inspection;
use Modules\CleanQuality\Entities\InspectionItem;
use Modules\CleanQuality\Entities\InspectionTemplate;
use Modules\CleanQuality\Http\Requests\StoreInspectionRequest;
use Modules\CleanQuality\Http\Requests\UpdateInspectionRequest;
use Modules\CleanQuality\Support\Enums\InspectionStatus;

class InspectionController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Inspections';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('inspections', $this->user->modules));

            return $next($request);
        });
    }

    public function index()
    {
        abort_403(user()->permission('view_inspections') != 'all'
            && user()->permission('view_inspection') != 'all');

        $this->inspections = Inspection::with('inspector')
            ->orderByDesc('id')
            ->paginate(20);

        $this->statuses  = InspectionStatus::all();
        $this->inspectors = User::allEmployees();

        return view('inspection::inspections.index', $this->data);
    }

    public function create()
    {
        abort_403(user()->permission('create_inspection') != 'all'
            && user()->permission('add_inspection') != 'all');

        $this->inspectors = User::allEmployees();
        $this->templates  = InspectionTemplate::where('is_active', true)->orderBy('name')->get();
        $this->statuses   = InspectionStatus::all();

        return view('inspection::inspections.create', $this->data);
    }

    public function store(StoreInspectionRequest $request)
    {
        abort_403(user()->permission('create_inspection') != 'all'
            && user()->permission('add_inspection') != 'all');

        $inspection = Inspection::create(array_merge(
            $request->safe()->except(['items']),
            ['company_id' => $this->company->id ?? null]
        ));

        if ($request->filled('items') && is_array($request->items)) {
            foreach ($request->items as $item) {
                $inspection->items()->create([
                    'area'   => $item['area'],
                    'passed' => (bool) ($item['passed'] ?? false),
                    'notes'  => $item['notes'] ?? null,
                ]);
            }
        }

        return redirect()
            ->route('inspections.show', $inspection->id)
            ->with('success', __('inspection::messages.inspection_created'));
    }

    public function show($id)
    {
        abort_403(user()->permission('view_inspections') != 'all'
            && user()->permission('view_inspection') != 'all');

        $this->inspection = Inspection::with(['inspector', 'items', 'template', 'approvedBy'])
            ->findOrFail($id);

        $this->pageTitle = 'Inspection #' . $id;

        return view('inspection::inspections.show', $this->data);
    }

    public function edit($id)
    {
        abort_403(user()->permission('edit_inspection') != 'all');

        $this->inspection = Inspection::with('items')->findOrFail($id);
        $this->inspectors = User::allEmployees();
        $this->templates  = InspectionTemplate::where('is_active', true)->orderBy('name')->get();
        $this->statuses   = InspectionStatus::all();

        return view('inspection::inspections.edit', $this->data);
    }

    public function update(UpdateInspectionRequest $request, $id)
    {
        abort_403(user()->permission('edit_inspection') != 'all');

        $inspection = Inspection::findOrFail($id);
        $inspection->update($request->safe()->except(['items']));

        if ($request->filled('items') && is_array($request->items)) {
            $keepIds = [];

            foreach ($request->items as $item) {
                if (!empty($item['id'])) {
                    $inspItem = InspectionItem::find($item['id']);
                    if ($inspItem && $inspItem->inspection_id == $inspection->id) {
                        $inspItem->update([
                            'area'   => $item['area'],
                            'passed' => (bool) ($item['passed'] ?? false),
                            'notes'  => $item['notes'] ?? null,
                        ]);
                        $keepIds[] = $inspItem->id;
                        continue;
                    }
                }

                $newItem = $inspection->items()->create([
                    'area'   => $item['area'],
                    'passed' => (bool) ($item['passed'] ?? false),
                    'notes'  => $item['notes'] ?? null,
                ]);
                $keepIds[] = $newItem->id;
            }

            $inspection->items()->whereNotIn('id', $keepIds)->delete();
        }

        return redirect()
            ->route('inspections.show', $inspection->id)
            ->with('success', __('inspection::messages.inspection_updated'));
    }

    public function destroy($id)
    {
        abort_403(user()->permission('delete_inspection') != 'all');

        Inspection::findOrFail($id)->delete();

        return Reply::success(__('inspection::messages.inspection_deleted'));
    }

    /**
     * Approve an inspection and mark it as passed.
     */
    public function approve(Request $request, $id)
    {
        abort_403(user()->permission('approve_inspection') != 'all');

        $inspection = Inspection::findOrFail($id);

        $inspection->update([
            'status'      => InspectionStatus::PASSED,
            'approved_at' => now(),
            'approved_by' => user()->id,
        ]);

        return Reply::success(__('inspection::messages.inspection_approved'));
    }

    /**
     * Trigger a re-clean request from a failed inspection.
     */
    public function requestReclean(Request $request, $id)
    {
        abort_403(user()->permission('request_reclean') != 'all');

        $inspection = Inspection::findOrFail($id);
        abort_403($inspection->status !== InspectionStatus::FAILED);

        $inspection->update([
            'status' => InspectionStatus::RECLEAN_BOOKED,
        ]);

        return Reply::success(__('inspection::messages.reclean_requested'));
    }
}
