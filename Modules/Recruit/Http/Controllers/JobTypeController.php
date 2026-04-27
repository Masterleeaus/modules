<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use App\Models\BaseModel;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use Modules\Recruit\Entities\RecruitJobType;
use App\Http\Controllers\AccountBaseController;
use Modules\Recruit\Http\Requests\StoreJobType;

class JobTypeController extends AccountBaseController
{

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */

    public function create()
    {
        $this->jobTypes = RecruitJobType::all();

        return view('recruit::jobs.job-type', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return array
     */
    public function store(StoreJobType $request)
    {
        $group = new RecruitJobType();
        $group->job_type = $request->job_type;
        $group->save();

        if ($request->selectedStages != '') {
            $selectedStages = $request->selectedStages . ',' . $group->id;
            $selectedStages = explode(',', $selectedStages);
        }
        else {
            $selectedStages = [$group->id];
        }

        $jobTypes = RecruitJobType::all();
        $options = '';

        foreach ($jobTypes as $stage) {
            $checkSelected = '';

            if (in_array($stage->id, $selectedStages)) {
                $checkSelected = 'selected';
            }

            $options .= '<option ' . $checkSelected . '
            data-content="<span class=\'badge badge-pill badge-light border\'><div class=\'d-inline-block mr-1\'></div> ' . $stage->job_type . ' </span>"
            value="' . $stage->id . '"> ' . $stage->job_type . '</option>';
        }

        return Reply::successWithData(__('recruit::messages.jobTypeAdded'), ['data' => $options]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(StoreJobType $request, $id)
    {
        $group = RecruitJobType::findOrFail($id);
        $group->job_type = strip_tags($request->job_type);
        $group->save();

        $jobTypes = RecruitJobType::all();

        $options = BaseModel::options($jobTypes, null, 'job_type');

        return Reply::successWithData(__('messages.updateSuccess'), ['data' => $options]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        RecruitJobType::destroy($id);
        $jobTypes = RecruitJobType::all();

        $options = BaseModel::options($jobTypes, null, 'job_type');

        return Reply::successWithData(__('messages.deleteSuccess'), ['data' => $options]);
    }

}
