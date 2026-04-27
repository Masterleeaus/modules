<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use Modules\Recruit\Http\Requests\InterviewStage\StoreRequest;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Recruit\Entities\RecruitInterviewStage;

class InterviewStageController extends Controller
{

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */

    public function create(Request $request)
    {
        $selectedStages = $request->stage;
        $stages = RecruitInterviewStage::all();

        return view('recruit::jobs.interview-stage.create', compact('stages', 'selectedStages'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(StoreRequest $request)
    {
        $group = new RecruitInterviewStage();
        $group->name = $request->name;
        $group->save();

        if ($request->selectedStages != '') {
            $selectedStages = $request->selectedStages . ',' . $group->id;
            $selectedStages = explode(',', $selectedStages);
        }
        else {
            $selectedStages = [$group->id];
        }

        $stages = RecruitInterviewStage::all();

        $options = '';

        foreach ($stages as $stage) {
            $checkSelected = '';

            if (in_array($stage->id, $selectedStages)) {
                $checkSelected = 'selected';
            }

            $options .= '<option ' . $checkSelected . '
            data-content="<span class=\'badge badge-pill badge-light border\'><div class=\'d-inline-block mr-1\'></div> ' . $stage->name . ' </span>"
            value="' . $stage->id . '"> ' . $stage->name . '</option>';
        }

        return Reply::successWithData(__('recruit::modules.message.stageSelected'), ['data' => $options]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(StoreRequest $request, $id)
    {
        $group = RecruitInterviewStage::findOrFail($id);
        $group->name = strip_tags($request->name);
        $group->save();

        if ($request->selectedStages != '') {
            $selectedStages = $request->selectedStages . ',' . $group->id;
            $selectedStages = explode(',', $selectedStages);
        }
        else {
            $selectedStages = [$group->id];
        }

        $stages = RecruitInterviewStage::all();
        $options = '';

        foreach ($stages as $stage) {
            $checkSelected = '';

            if (in_array($stage->id, $selectedStages)) {
                $checkSelected = 'selected';
            }

            $options .= '<option ' . $checkSelected . '
            data-content="<span class=\'badge badge-pill badge-light border\'><div class=\'d-inline-block mr-1\'></div> ' . $stage->name . ' </span>"
            value="' . $stage->id . '"> ' . $stage->name . '</option>';
        }

        return Reply::successWithData(__('messages.updateSuccess'), ['data' => $options]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        RecruitInterviewStage::destroy($id);

        return Reply::success(__('recruit::modules.message.deleteSuccess'));
    }

}
