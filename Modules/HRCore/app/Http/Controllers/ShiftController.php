<?php

namespace Modules\HRCore\app\Http\Controllers;

use App\ApiClasses\Error;
use App\ApiClasses\Success;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\HRCore\app\Models\Shift;
use Yajra\DataTables\Facades\DataTables;

class ShiftController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrcore.view-shifts', ['only' => ['index', 'datatable', 'show', 'list']]);
        $this->middleware('permission:hrcore.create-shifts', ['only' => ['create', 'store']]);
        $this->middleware('permission:hrcore.edit-shifts', ['only' => ['edit', 'update', 'toggleStatus']]);
        $this->middleware('permission:hrcore.delete-shifts', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('hrcore::shift.index');
    }

    public function datatable(Request $request)
    {
        try {
            $query = Shift::query();

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('shift_days', function ($shift) {
                    $daysHtml = '<div class="d-flex justify-content-start flex-wrap gap-1">';
                    $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                    foreach ($days as $day) {
                        $label = ucfirst(substr($day, 0, 3));
                        $class = $shift->$day ? 'bg-label-success' : 'bg-label-secondary';
                        $daysHtml .= '<span class="badge '.$class.'">'.$label.'</span>';
                    }
                    $daysHtml .= '</div>';

                    return $daysHtml;
                })
                ->addColumn('shift_time', function ($shift) {
                    $startTime = Carbon::parse($shift->start_time)->format('h:i A');
                    $endTime = Carbon::parse($shift->end_time)->format('h:i A');

                    return $startTime.' - '.$endTime;
                })
                ->addColumn('status', function ($shift) {
                    $statusClass = $shift->status === Status::ACTIVE ? 'success' : 'secondary';

                    return '<span class="badge bg-label-'.$statusClass.'">'.ucfirst($shift->status->value).'</span>';
                })
                ->addColumn('actions', function ($shift) {
                    $actions = [];

                    // Edit action
                    if (auth()->user()->can('hrcore.edit-shifts')) {
                        $actions[] = [
                            'label' => __('Edit'),
                            'icon' => 'bx bx-edit',
                            'onclick' => "editShift({$shift->id})",
                        ];
                    }

                    // Status toggle action
                    if (auth()->user()->can('hrcore.edit-shifts')) {
                        $actions[] = [
                            'label' => $shift->status === Status::ACTIVE ? __('Deactivate') : __('Activate'),
                            'icon' => $shift->status === Status::ACTIVE ? 'bx bx-x' : 'bx bx-check',
                            'onclick' => "toggleStatus({$shift->id})",
                        ];
                    }

                    // Delete action
                    if (auth()->user()->can('hrcore.delete-shifts')) {
                        // Check if shift is assigned to users
                        $isAssigned = $shift->users()->exists();

                        if (! empty($actions)) {
                            $actions[] = ['divider' => true];
                        }

                        $deleteAction = [
                            'label' => __('Delete'),
                            'icon' => 'bx bx-trash',
                            'onclick' => "deleteShift({$shift->id})",
                        ];

                        if ($isAssigned) {
                            $deleteAction['disabled'] = true;
                            $deleteAction['title'] = __('Cannot delete shift assigned to users');
                        }

                        $actions[] = $deleteAction;
                    }

                    return view('components.datatable-actions', [
                        'id' => $shift->id,
                        'actions' => $actions,
                    ])->render();
                })
                ->rawColumns(['shift_days', 'shift_time', 'status', 'actions'])
                ->make(true);
        } catch (Exception $e) {
            Log::error('Shift datatable error: '.$e->getMessage());

            return Error::response('Something went wrong');
        }
    }

    public function create()
    {
        return redirect()->route('hrcore.shifts.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:shifts,code',
            'notes' => 'nullable|string|max:500',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'sunday' => 'boolean',
            'monday' => 'boolean',
            'tuesday' => 'boolean',
            'wednesday' => 'boolean',
            'thursday' => 'boolean',
            'friday' => 'boolean',
            'saturday' => 'boolean',
        ]);

        if ($validator->fails()) {
            return Error::response($validator->errors()->first(), 422);
        }

        DB::beginTransaction();
        try {
            $shift = Shift::create([
                'name' => $request->name,
                'code' => $request->code,
                'notes' => $request->notes,
                'start_time' => Carbon::parse($request->start_time)->format('H:i:s'),
                'end_time' => Carbon::parse($request->end_time)->format('H:i:s'),
                'sunday' => $request->boolean('sunday'),
                'monday' => $request->boolean('monday'),
                'tuesday' => $request->boolean('tuesday'),
                'wednesday' => $request->boolean('wednesday'),
                'thursday' => $request->boolean('thursday'),
                'friday' => $request->boolean('friday'),
                'saturday' => $request->boolean('saturday'),
                'status' => Status::ACTIVE,
            ]);

            DB::commit();

            return Success::response([
                'message' => 'Shift created successfully!',
                'shift' => $shift,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Shift creation failed: '.$e->getMessage());

            return Error::response('Failed to create shift. Please try again.');
        }
    }

    public function show($id)
    {
        try {
            $shift = Shift::findOrFail($id);

            // Format times for display
            $shift->start_time_formatted = Carbon::parse($shift->start_time)->format('H:i');
            $shift->end_time_formatted = Carbon::parse($shift->end_time)->format('H:i');

            return Success::response($shift);
        } catch (Exception $e) {
            return Error::response('Shift not found', 404);
        }
    }

    public function edit($id)
    {
        return redirect()->route('hrcore.shifts.index');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('shifts')->ignore($id)],
            'notes' => 'nullable|string|max:500',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'sunday' => 'boolean',
            'monday' => 'boolean',
            'tuesday' => 'boolean',
            'wednesday' => 'boolean',
            'thursday' => 'boolean',
            'friday' => 'boolean',
            'saturday' => 'boolean',
        ]);

        if ($validator->fails()) {
            return Error::response($validator->errors()->first(), 422);
        }

        DB::beginTransaction();
        try {
            $shift = Shift::findOrFail($id);

            $shift->update([
                'name' => $request->name,
                'code' => $request->code,
                'notes' => $request->notes,
                'start_time' => Carbon::parse($request->start_time)->format('H:i:s'),
                'end_time' => Carbon::parse($request->end_time)->format('H:i:s'),
                'sunday' => $request->boolean('sunday'),
                'monday' => $request->boolean('monday'),
                'tuesday' => $request->boolean('tuesday'),
                'wednesday' => $request->boolean('wednesday'),
                'thursday' => $request->boolean('thursday'),
                'friday' => $request->boolean('friday'),
                'saturday' => $request->boolean('saturday'),
            ]);

            DB::commit();

            return Success::response([
                'message' => 'Shift updated successfully!',
                'shift' => $shift,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Shift update failed: '.$e->getMessage());

            return Error::response('Failed to update shift. Please try again.');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $shift = Shift::findOrFail($id);

            // Check if shift has users
            if ($shift->users()->exists()) {
                return Error::response('Cannot delete shift that is assigned to users.');
            }

            $shift->delete();

            DB::commit();

            return Success::response([
                'message' => 'Shift deleted successfully!',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Shift deletion failed: '.$e->getMessage());

            return Error::response('Failed to delete shift. Please try again.');
        }
    }

    public function toggleStatus($id)
    {
        DB::beginTransaction();
        try {
            $shift = Shift::findOrFail($id);

            $shift->status = $shift->status === Status::ACTIVE ? Status::INACTIVE : Status::ACTIVE;
            $shift->save();

            DB::commit();

            return Success::response([
                'message' => 'Shift status updated successfully!',
                'status' => $shift->status,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Shift status toggle failed: '.$e->getMessage());

            return Error::response('Failed to update shift status. Please try again.');
        }
    }

    public function list()
    {
        $shifts = Shift::where('status', Status::ACTIVE)
            ->get(['id', 'name', 'code']);

        return Success::response($shifts);
    }

    // Legacy route handlers for backward compatibility
    public function listAjax(Request $request)
    {
        return $this->datatable($request);
    }

    public function getActiveShiftsForDropdown()
    {
        return $this->list();
    }
}
