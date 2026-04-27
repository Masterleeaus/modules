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
use Illuminate\Validation\Rule;
use Modules\HRCore\app\Models\Department;
use Yajra\DataTables\Facades\DataTables;

class DepartmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrcore.view-departments', ['only' => ['index', 'indexAjax', 'show', 'getListAjax', 'getParentDepartments']]);
        $this->middleware('permission:hrcore.create-departments', ['only' => ['create', 'store', 'addOrUpdateDepartmentAjax']]);
        $this->middleware('permission:hrcore.edit-departments', ['only' => ['edit', 'update', 'toggleStatus', 'changeStatus', 'getDepartmentAjax']]);
        $this->middleware('permission:hrcore.delete-departments', ['only' => ['destroy', 'deleteAjax']]);
    }

    public function index()
    {
        return view('hrcore::departments.index');
    }

    public function getListAjax()
    {
        $departments = Department::where('status', Status::ACTIVE)
            ->get(['id', 'name', 'code']);

        return Success::response($departments);
    }

    public function indexAjax(Request $request)
    {
        $query = Department::with('parentDepartment:id,name');

        return DataTables::of($query)
            ->addColumn('parent_id', function ($department) {
                return $department->parentDepartment ? $department->parentDepartment->name : __('No Parent');
            })
            ->addColumn('notes', function ($department) {
                return $department->notes ?? '';
            })
            ->addColumn('status', function ($department) {
                return $department->status->badge();
            })
            ->addColumn('action', function ($department) {
                $actions = [];

                // Edit action
                if (auth()->user()->can('hrcore.edit-departments')) {
                    $actions[] = [
                        'label' => __('Edit'),
                        'icon' => 'bx bx-edit',
                        'onclick' => "editDepartment({$department->id})",
                    ];
                }

                // Status toggle action
                if (auth()->user()->can('hrcore.edit-departments')) {
                    $actions[] = [
                        'label' => $department->status === Status::ACTIVE ? __('Deactivate') : __('Activate'),
                        'icon' => $department->status === Status::ACTIVE ? 'bx bx-x' : 'bx bx-check',
                        'onclick' => "toggleDepartmentStatus({$department->id})",
                    ];
                }

                // Delete action
                if (auth()->user()->can('hrcore.delete-departments')) {
                    if (! empty($actions)) {
                        $actions[] = ['divider' => true];
                    }
                    $actions[] = [
                        'label' => __('Delete'),
                        'icon' => 'bx bx-trash',
                        'onclick' => "deleteDepartment({$department->id})",
                    ];
                }

                return view('components.datatable-actions', [
                    'id' => $department->id,
                    'actions' => $actions,
                ])->render();
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function getParentDepartments()
    {
        $departments = Department::where('status', Status::ACTIVE)
            ->with('parentDepartment:id,name')
            ->get(['id', 'name', 'parent_id']);

        return Success::response($departments);
    }

    public function create()
    {
        $departments = Department::where('status', Status::ACTIVE)->get(['id', 'name']);

        return view('hrcore::departments.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:departments,code',
            'notes' => 'nullable|string|max:225',
            'parent_department' => 'nullable|exists:departments,id',
        ]);

        try {
            DB::beginTransaction();

            $department = Department::create([
                'name' => $validatedData['name'],
                'code' => $validatedData['code'],
                'notes' => $validatedData['notes'],
                'parent_id' => $validatedData['parent_department'],
                'status' => Status::ACTIVE,
            ]);

            DB::commit();

            return Success::response(__('Department created successfully!'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Department creation failed: '.$e->getMessage());

            return Error::response(__('Failed to create department. Please try again.'));
        }
    }

    public function show($id)
    {
        $department = Department::with('parentDepartment:id,name')->findOrFail($id);

        return view('hrcore::departments.show', compact('department'));
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        $departments = Department::where('status', Status::ACTIVE)
            ->where('id', '!=', $id)
            ->get(['id', 'name']);

        return view('hrcore::departments.edit', compact('department', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('departments')->ignore($id),
            ],
            'notes' => 'nullable|string|max:225',
            'parent_department' => 'nullable|exists:departments,id',
        ]);

        try {
            DB::beginTransaction();

            $department = Department::findOrFail($id);
            $department->update([
                'name' => $validatedData['name'],
                'code' => $validatedData['code'],
                'notes' => $validatedData['notes'],
                'parent_id' => $validatedData['parent_department'],
            ]);

            DB::commit();

            return Success::response(__('Department updated successfully!'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Department update failed: '.$e->getMessage());

            return Error::response(__('Failed to update department. Please try again.'));
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $department = Department::findOrFail($id);

            // Check if department has children
            if ($department->children()->exists()) {
                return Error::response(__('Cannot delete department that has sub-departments.'));
            }

            $department->delete();

            DB::commit();

            return Success::response(__('Department deleted successfully!'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Department deletion failed: '.$e->getMessage());

            return Error::response(__('Failed to delete department. Please try again.'));
        }
    }

    public function toggleStatus($id)
    {
        try {
            DB::beginTransaction();

            $department = Department::findOrFail($id);
            $department->status = $department->status == Status::ACTIVE ? Status::INACTIVE : Status::ACTIVE;
            $department->save();

            DB::commit();

            return Success::response(__('Department status updated successfully!'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Department status update failed: '.$e->getMessage());

            return Error::response(__('Failed to update department status. Please try again.'));
        }
    }

    // Legacy method for backward compatibility
    public function addOrUpdateDepartmentAjax(Request $request)
    {
        $departmentId = $request->input('departmentId', null);

        if ($departmentId) {
            return $this->update($request, $departmentId);
        } else {
            return $this->store($request);
        }
    }

    // Legacy method for backward compatibility
    public function getDepartmentAjax($id)
    {
        try {
            $department = Department::findOrFail($id);

            $response = [
                'id' => $department->id,
                'name' => $department->name,
                'code' => $department->code,
                'notes' => $department->notes,
                'parent_id' => $department->parent_id,
                'status' => $department->status,
            ];

            return Success::response($response);
        } catch (Exception $e) {
            return Error::response($e->getMessage());
        }
    }

    // Legacy method for backward compatibility
    public function deleteAjax($id)
    {
        return $this->destroy($id);
    }

    // Legacy method for backward compatibility
    public function changeStatus($id)
    {
        return $this->toggleStatus($id);
    }
}
