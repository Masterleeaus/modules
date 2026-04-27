<?php

namespace Modules\HRCore\app\Http\Controllers;

use App\ApiClasses\Error;
use App\ApiClasses\Success;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\HRCore\app\Models\Team;
use Yajra\DataTables\Facades\DataTables;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrcore.view-teams', ['only' => ['index', 'datatable', 'show', 'list', 'getTeamsListAjax', 'getTeamAjax', 'getTeamListAjax']]);
        $this->middleware('permission:hrcore.create-teams', ['only' => ['create', 'store', 'checkCode', 'checkCodeValidationAjax', 'addOrUpdateTeamAjax']]);
        $this->middleware('permission:hrcore.edit-teams', ['only' => ['edit', 'update', 'toggleStatus', 'changeStatus']]);
        $this->middleware('permission:hrcore.delete-teams', ['only' => ['destroy', 'deleteTeamAjax']]);
    }

    public function index()
    {
        $teamHeads = User::where('status', 'active')->get(['id', 'first_name', 'last_name']);

        return view('hrcore::teams.index', compact('teamHeads'));
    }

    public function datatable(Request $request)
    {
        try {
            $query = Team::query()
                ->with('teamHead:id,first_name,last_name');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('team_head', function ($team) {
                    if ($team->teamHead) {
                        return $team->teamHead->first_name.' '.$team->teamHead->last_name;
                    }

                    return 'N/A';
                })
                ->addColumn('status', function ($team) {
                    $statusClass = $team->status === Status::ACTIVE ? 'success' : 'secondary';

                    return '<span class="badge bg-label-'.$statusClass.'">'.ucfirst($team->status->value).'</span>';
                })
                ->addColumn('actions', function ($team) {
                    $actions = [];

                    // Edit action
                    if (auth()->user()->can('hrcore.edit-teams')) {
                        $actions[] = [
                            'label' => __('Edit'),
                            'icon' => 'bx bx-edit',
                            'onclick' => "editTeam({$team->id})",
                        ];
                    }

                    // Status toggle action
                    if (auth()->user()->can('hrcore.edit-teams')) {
                        $actions[] = [
                            'label' => $team->status === Status::ACTIVE ? __('Deactivate') : __('Activate'),
                            'icon' => $team->status === Status::ACTIVE ? 'bx bx-x' : 'bx bx-check',
                            'onclick' => "toggleStatus({$team->id})",
                        ];
                    }

                    // Delete action
                    if (auth()->user()->can('hrcore.delete-teams')) {
                        if (! empty($actions)) {
                            $actions[] = ['divider' => true];
                        }
                        $actions[] = [
                            'label' => __('Delete'),
                            'icon' => 'bx bx-trash',
                            'onclick' => "deleteTeam({$team->id})",
                        ];
                    }

                    return view('components.datatable-actions', [
                        'id' => $team->id,
                        'actions' => $actions,
                    ])->render();
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        } catch (Exception $e) {
            Log::error('Team datatable error: '.$e->getMessage());

            return Error::response('Something went wrong');
        }
    }

    public function create()
    {
        return redirect()->route('hrcore.teams.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:teams,code',
            'team_head_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return Error::response($validator->errors()->first(), 422);
        }

        DB::beginTransaction();
        try {
            $team = Team::create([
                'name' => $request->name,
                'code' => $request->code,
                'team_head_id' => $request->team_head_id,
                'notes' => $request->notes,
                'status' => Status::ACTIVE,
            ]);

            DB::commit();

            return Success::response([
                'message' => 'Team created successfully!',
                'team' => $team,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Team creation failed: '.$e->getMessage());

            return Error::response('Failed to create team. Please try again.');
        }
    }

    public function show($id)
    {
        try {
            $team = Team::with('teamHead:id,first_name,last_name')->findOrFail($id);

            return Success::response($team);
        } catch (Exception $e) {
            return Error::response('Team not found', 404);
        }
    }

    public function edit($id)
    {
        return redirect()->route('hrcore.teams.index');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('teams')->ignore($id)],
            'team_head_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return Error::response($validator->errors()->first(), 422);
        }

        DB::beginTransaction();
        try {
            $team = Team::findOrFail($id);

            $team->update([
                'name' => $request->name,
                'code' => $request->code,
                'team_head_id' => $request->team_head_id,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return Success::response([
                'message' => 'Team updated successfully!',
                'team' => $team,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Team update failed: '.$e->getMessage());

            return Error::response('Failed to update team. Please try again.');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $team = Team::findOrFail($id);

            // Check if team has users
            if ($team->users()->exists()) {
                return Error::response('Cannot delete team that has members.');
            }

            $team->delete();

            DB::commit();

            return Success::response([
                'message' => 'Team deleted successfully!',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Team deletion failed: '.$e->getMessage());

            return Error::response('Failed to delete team. Please try again.');
        }
    }

    public function toggleStatus($id)
    {
        DB::beginTransaction();
        try {
            $team = Team::findOrFail($id);

            $team->status = $team->status === Status::ACTIVE ? Status::INACTIVE : Status::ACTIVE;
            $team->save();

            DB::commit();

            return Success::response([
                'message' => 'Team status updated successfully!',
                'status' => $team->status,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Team status toggle failed: '.$e->getMessage());

            return Error::response('Failed to update team status. Please try again.');
        }
    }

    public function list()
    {
        $teams = Team::where('status', Status::ACTIVE)
            ->with('teamHead:id,first_name,last_name')
            ->get(['id', 'name', 'code', 'team_head_id']);

        return Success::response($teams);
    }

    public function checkCode(Request $request)
    {
        $code = $request->code;
        $id = $request->id;

        if (! $code) {
            return response()->json(['valid' => false]);
        }

        $query = Team::where('code', $code);

        if ($id) {
            $query->where('id', '!=', $id);
        }

        $exists = $query->exists();

        return response()->json(['valid' => ! $exists]);
    }

    // Legacy route handlers
    public function getTeamsListAjax(Request $request)
    {
        return $this->datatable($request);
    }

    public function addOrUpdateTeamAjax(Request $request)
    {
        if ($request->id) {
            return $this->update($request, $request->id);
        }

        return $this->store($request);
    }

    public function getTeamAjax($id)
    {
        return $this->show($id);
    }

    public function deleteTeamAjax($id)
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

    public function getTeamListAjax()
    {
        return $this->list();
    }
}
