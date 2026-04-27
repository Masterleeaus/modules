<?php

namespace Modules\HRCore\app\Http\Controllers;

use App\ApiClasses\Error;
use App\ApiClasses\Success;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\HRCore\app\Models\Department;
use Modules\HRCore\app\Models\Designation;
use Yajra\DataTables\Facades\DataTables;

class DesignationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrcore.view-designations', ['only' => ['index', 'datatable', 'show', 'list', 'indexAjax', 'getByIdAjax', 'getDesignationListAjax']]);
        $this->middleware('permission:hrcore.create-designations', ['only' => ['create', 'store', 'checkCode', 'checkCodeValidationAjax', 'addOrUpdateAjax']]);
        $this->middleware('permission:hrcore.edit-designations', ['only' => ['edit', 'update', 'toggleStatus', 'changeStatus']]);
        $this->middleware('permission:hrcore.delete-designations', ['only' => ['destroy', 'deleteAjax']]);
    }

    public function index()
    {
        $departments = Department::where('status', Status::ACTIVE)->get(['id', 'name']);

        return view('hrcore::designation.index', compact('departments'));
    }

    public function datatable(Request $request)
    {
        try {
            $query = Designation::query()
                ->with('department:id,name');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('department', function ($designation) {
                    return $designation->department ? $designation->department->name : 'N/A';
                })
                ->addColumn('status', function ($designation) {
                    $statusClass = $designation->status === Status::ACTIVE ? 'success' : 'secondary';

                    return '<span class="badge bg-label-'.$statusClass.'">'.ucfirst($designation->status->value).'</span>';
                })
                ->addColumn('actions', function ($designation) {
                    $actions = [];

                    // Edit action
                    if (auth()->user()->can('hrcore.edit-designations')) {
                        $actions[] = [
                            'label' => __('Edit'),
                            'icon' => 'bx bx-edit',
                            'onclick' => "editDesignation({$designation->id})",
                        ];
                    }

                    // Status toggle action
                    if (auth()->user()->can('hrcore.edit-designations')) {
                        $actions[] = [
                            'label' => $designation->status === Status::ACTIVE ? __('Deactivate') : __('Activate'),
                            'icon' => $designation->status === Status::ACTIVE ? 'bx bx-x' : 'bx bx-check',
                            'onclick' => "toggleStatus({$designation->id})",
                        ];
                    }

                    // Delete action
                    if (auth()->user()->can('hrcore.delete-designations')) {
                        if (! empty($actions)) {
                            $actions[] = ['divider' => true];
                        }
                        $actions[] = [
                            'label' => __('Delete'),
                            'icon' => 'bx bx-trash',
                            'onclick' => "deleteDesignation({$designation->id})",
                        ];
                    }

                    return view('components.datatable-actions', [
                        'id' => $designation->id,
                        'actions' => $actions,
                    ])->render();
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        } catch (Exception $e) {
            Log::error('Designation datatable error: '.$e->getMessage());

            return Error::response('Something went wrong');
        }
    }

    public function create()
    {
        return redirect()->route('hrcore.designations.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:designations,code',
            'department_id' => 'nullable|exists:departments,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return Error::response($validator->errors()->first(), 422);
        }

        DB::beginTransaction();
        try {
            $designation = Designation::create([
                'name' => $request->name,
                'code' => $request->code,
                'department_id' => $request->department_id,
                'notes' => $request->notes,
                'status' => Status::ACTIVE,
            ]);

            DB::commit();

            return Success::response([
                'message' => 'Designation created successfully!',
                'designation' => $designation,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Designation creation failed: '.$e->getMessage());

            return Error::response('Failed to create designation. Please try again.');
        }
    }

    public function show($id)
    {
        try {
            $designation = Designation::with('department:id,name')->findOrFail($id);

            return Success::response($designation);
        } catch (Exception $e) {
            return Error::response('Designation not found', 404);
        }
    }

    public function edit($id)
    {
        return redirect()->route('hrcore.designations.index');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('designations')->ignore($id)],
            'department_id' => 'nullable|exists:departments,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return Error::response($validator->errors()->first(), 422);
        }

        DB::beginTransaction();
        try {
            $designation = Designation::findOrFail($id);

            $designation->update([
                'name' => $request->name,
                'code' => $request->code,
                'department_id' => $request->department_id,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return Success::response([
                'message' => 'Designation updated successfully!',
                'designation' => $designation,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Designation update failed: '.$e->getMessage());

            return Error::response('Failed to update designation. Please try again.');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $designation = Designation::findOrFail($id);

            // Check if designation is in use
            if ($designation->users()->exists()) {
                return Error::response('Cannot delete designation that is assigned to users.');
            }

            $designation->delete();

            DB::commit();

            return Success::response([
                'message' => 'Designation deleted successfully!',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Designation deletion failed: '.$e->getMessage());

            return Error::response('Failed to delete designation. Please try again.');
        }
    }

    public function toggleStatus($id)
    {
        DB::beginTransaction();
        try {
            $designation = Designation::findOrFail($id);

            $designation->status = $designation->status === Status::ACTIVE ? Status::INACTIVE : Status::ACTIVE;
            $designation->save();

            DB::commit();

            return Success::response([
                'message' => 'Designation status updated successfully!',
                'status' => $designation->status,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Designation status toggle failed: '.$e->getMessage());

            return Error::response('Failed to update designation status. Please try again.');
        }
    }

    public function list()
    {
        $designations = Designation::where('status', Status::ACTIVE)
            ->with('department:id,name')
            ->get(['id', 'name', 'code', 'department_id']);

        return Success::response($designations);
    }

    public function checkCode(Request $request)
    {
        $code = $request->code;
        $id = $request->id;

        if (! $code) {
            return response()->json(['valid' => false]);
        }

        $query = Designation::where('code', $code);

        if ($id) {
            $query->where('id', '!=', $id);
        }

        $exists = $query->exists();

        return response()->json(['valid' => ! $exists]);
    }

    // Legacy route handlers
    public function indexAjax(Request $request)
    {
        return $this->datatable($request);
    }

    public function addOrUpdateAjax(Request $request)
    {
        if ($request->id) {
            return $this->update($request, $request->id);
        }

        return $this->store($request);
    }

    public function getByIdAjax($id)
    {
        return $this->show($id);
    }

    public function deleteAjax($id)
    {
        return $this->destroy($id);
    }

    public function changeStatus($id)
    {
        return $this->toggleStatus($id);
    }

    public function checkCodeValidationAjax(Request $request)
    {
        return $this->checkCode($request);
    }

    public function getDesignationListAjax()
    {
        return $this->list();
    }
}
