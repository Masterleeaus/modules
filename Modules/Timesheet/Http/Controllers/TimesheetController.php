<?php

namespace Modules\Timesheet\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Modules\Timesheet\Entities\Timesheet;
use Modules\Timesheet\Events\CreateTimesheet;
use Modules\Timesheet\Events\DestroyTimesheet;
use Modules\Timesheet\Events\UpdateTimesheet;

class TimesheetController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if(\Auth::user()->isAbleTo('timesheet manage'))
        {
            if(Auth::user()->type == 'company'){

                $timesheets = Timesheet::select('timesheets.*', 'users.name as user_name','projects.name as project_name','tasks.title as task_title')->leftJoin('users', 'timesheets.user_id', '=', 'users.id')->leftJoin('tasks', 'timesheets.task_id', '=', 'tasks.id')->leftJoin('projects', 'timesheets.project_id', '=', 'projects.id')->where('timesheets.created_by', creatorId())->where('timesheets.workspace_id',getActiveWorkSpace())->get();
            }
            else{
                $timesheets = Timesheet::where('user_id', Auth::user()->id)->where('workspace_id',Auth::user()->active_workspace)->get();
            }

            return view('timesheet::timesheet.index', compact('timesheets'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if(\Auth::user()->isAbleTo('timesheet create'))
        {
            $user = User::where('workspace_id', getActiveWorkSpace())->where('created_by', '=', creatorId())->emp()->get()->pluck('name', 'id');
            $project='';
            if (module_is_active('Taskly')){
                if(Auth::user()->type == 'company'){
                    $project = \Modules\Taskly\Entities\Project::where('workspace', getActiveWorkSpace())->where('created_by', '=', creatorId())->projectonly()->get()->pluck('name','id');
                }
                else{
                    $userproject = \Modules\Taskly\Entities\UserProject::where('user_id', '=', Auth::user()->id)->get()->pluck('project_id');
                    $project = \Modules\Taskly\Entities\Project::whereIn('id',$userproject)->projectonly()->get()->pluck('name','id');
                }
            }
            $mytime = \Carbon\Carbon::now();
            $date = $mytime->toDateString();
            return view('timesheet::timesheet.create', compact('user','date','project'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if(\Auth::user()->isAbleTo('timesheet create'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                    'date' => 'required',
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            if($request->user_id)
            {
                $user_id = $request->user_id;
            }
            else{
                $user_id = Auth::user()->id;
            }

            $timesheet                 = new Timesheet();
            $timesheet['user_id']      = $user_id;
            $timesheet['project_id']   = $request->project_id;
            $timesheet['task_id']      = $request->task_id;
            $timesheet['date']         = $request->date;
            $timesheet['hours']        = $request->hours;
            $timesheet['minutes']      = $request->minutes;
            $timesheet['type']         = $request->type;
            $timesheet['notes']        = $request->notes;
            $timesheet['workspace_id'] = getActiveWorkSpace();
            $timesheet['created_by']   = creatorId();
            $timesheet->save();

            event(new CreateTimesheet($request,$timesheet));

            return redirect()->back()->with('success', __('Timesheet Successfully Created.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('timesheet::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Timesheet $timesheet)
    {
        if(\Auth::user()->isAbleTo('timesheet edit'))
        {
            $user = User::where('workspace_id', getActiveWorkSpace())->where('created_by', '=', creatorId())->emp()->get()->pluck('name', 'id');
            $project  = \Modules\Taskly\Entities\Project::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->projectonly()->get()->pluck('name', 'id');
            $task = \Modules\Taskly\Entities\Task::where('project_id',$timesheet->project_id)->get()->pluck('title','id');
            $hours = $timesheet->hours;
            $minutes = $timesheet->minutes;
            return view('timesheet::timesheet.edit', compact('timesheet','user','hours','minutes','project','task'));
        }
        else{
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request,Timesheet $timesheet)
    {
        if(\Auth::user()->isAbleTo('timesheet edit'))
        {
            if($timesheet->created_by == creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                'date' => 'required',
                                'hours' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('timesheet.index')->with('error', $messages->first());
                }

                if($request->user_id)
                {
                    $user_id = $request->user_id;
                }
                else{
                    $user_id = Auth::user()->id;
                }

                $timesheet['user_id']      = $user_id;
                $timesheet['project_id']   = $request->project_id;
                $timesheet['task_id']      = $request->task_id;
                $timesheet['date']         = $request->date;
                $timesheet['hours']        = $request->hours;
                $timesheet['minutes']      = $request->minutes;
                $timesheet['type']         = $request->type;
                $timesheet['notes']        = $request->notes;
                $timesheet['workspace_id'] = getActiveWorkSpace();
                $timesheet['created_by']   = creatorId();
                $timesheet->save();

                event(new UpdateTimesheet($request,$timesheet));

                return redirect()->back()->with('success', __('Timesheet successfully updated!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Timesheet $timesheet)
    {
        if(\Auth::user()->isAbleTo('timesheet delete'))
        {
            event(new DestroyTimesheet($timesheet));

            $timesheet->delete();

            return redirect()->back()->with('success', __('Timesheet Successfully Deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }


    public function gethours(Request $request,$id)
    {
        if($request->user_id)
        {
            $user_id = $request->user_id;
        }
        else{
            $user_id = Auth::user()->id;
        }
        if(module_is_active('Hrm'))
        {
            $attendance = \Modules\Hrm\Entities\Attendance::where('employee_id', '=', $user_id)->where('date',$request->date)->first();
            if(!empty($attendance)){
                $start = strtotime($attendance->clock_in);
                $end = strtotime($attendance->clock_out);
                $total = $end - $start;
                $time = date("H:i", $total);

                $time = explode(":", $time);
                $timesheets = Timesheet::where('user_id',$user_id)->whereDate('date',$request->date)->where('type','clock in/clock out')->get();

                $timesheetshours = 0;
                $timesheetsminutes = 0;
                foreach($timesheets as $value){

                    $timesheetshours = $timesheetshours + $value->hours;
                    $timesheetsminutes = $timesheetsminutes + $value->minutes;
                }
                $hourss = $time[0]-$timesheetshours;
                $minutess = $time[1]-$timesheetsminutes;
                $time = Timesheet::find($id);
                $hours = $time->hours+ $hourss;
                $minutes = $time->minutes+ $minutess;
                $array = array($hours,$minutes);
                $hours = implode(":", $array);
            }
            else{
                $hours = date("H:i", 0);
            }
        }
        else{
            $hours = date("H:i", 0);
        }
        return response()->json($hours);
    }

    public function totalhours(Request $request)
    {
        if($request->user_id != null)
        {
            $user_id = $request->user_id;
        }
        else{
            $user_id = Auth::user()->id;
        }
        if(module_is_active('Hrm'))
        {
            $attendance = \Modules\Hrm\Entities\Attendance::where('employee_id', '=', $user_id)->where('date',$request->date)->first();
            if(!empty($attendance)){
                $start = strtotime($attendance->clock_in);
                $end = strtotime($attendance->clock_out);
                $total = $end - $start;
                $time = date("H:i", $total);
                $time = explode(":", $time);
                $timesheets = Timesheet::where('user_id',$user_id)->whereDate('date',$request->date)->where('type','clock in/clock out')->get();

                $timesheetshours = 0;
                $timesheetsminutes = 0;
                foreach($timesheets as $value){

                    $timesheetshours = $timesheetshours + $value->hours;
                    $timesheetsminutes = $timesheetsminutes + $value->minutes;
                }
                $hours = $time[0]-$timesheetshours;
                $minutes = $time[1]-$timesheetsminutes;
                $array = array($hours,$minutes);
                $hours = implode(":", $array);
            }
            else{
                $hours = date("H:i", 0);
            }
        }
        else{
            $hours = date("H:i", 0);
        }
        return response()->json($hours);
    }
    public function gettask(Request $request)
    {
        $task = \Modules\Taskly\Entities\Task::where('project_id',$request->project_id)->get()->pluck('title','id');
        return response()->json($task);
    }
}
