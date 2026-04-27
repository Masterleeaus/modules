<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Recruit\Entities\Recruiter;

use Modules\Recruit\Entities\RecruitSetting;
use Modules\Recruit\Http\Requests\Recruiter\StoreRecruiterRequest;

class RecruiterController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->activeSettingMenu = 'recruit_settings';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(RecruitSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $addPermission = user()->permission('add_recruiter');
        abort_403(!in_array($addPermission, ['all', 'added']));

        $this->employees = User::allEmployees(active:true)->all();
        $this->selectedRecruiter = Recruiter::get()->pluck('user_id')->toArray();

        return view('recruit::recruit-setting.create-recruiter-modal', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(StoreRecruiterRequest $request)
    {
        $users = $request->user_id;

        foreach ($users as $user) {
            $agent = new Recruiter();
            $agent->user_id = $user;
            $agent->status = 'enabled';
            $agent->added_by = user()->id;
            $agent->save();
        }

        $employees = $this->employees = Recruiter::with('user')->where('status', '=', 'enabled')->get();
        $options = '';

        foreach ($employees as $employee) {
            if ($employee && $employee->user) {
                $options .= '<option data-content="<div class=\'d-inline-block mr-1\'><img class=\'taskEmployeeImg rounded-circle\' src=' .  $employee->user->image_url . ' ></div> ' . $employee->user->name . '" value="' . $employee->user->id . '"> ' . $employee->user->name . ' </option>';
            }
        }

        return Reply::successWithData(__('recruit::messages.recruiterAdded'), ['data' => $options]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $agent = Recruiter::findOrFail($id);
        $agent->status = $request->status;
        $agent->last_updated_by = user()->id;
        $agent->save();

        return Reply::success(__('recruit::messages.recruiterUpdate'));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $deletePermission = user()->permission('delete_recruiter');
        abort_403(!in_array($deletePermission, ['all', 'added']));

        Recruiter::destroy($id);

        return Reply::success(__('recruit::messages.recruiterDelete'));
    }

    public function addRecruiter()
    {
        $this->recruiters = Recruiter::with('user')->get();

        $this->employees = User::allEmployees(active:true)->all();
        $this->selectedRecruiter = Recruiter::get()->pluck('user_id')->toArray();

        return view('recruit::jobs.recruiter.create', $this->data);
    }

}
