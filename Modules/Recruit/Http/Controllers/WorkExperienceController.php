<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Models\BaseModel;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Recruit\Entities\RecruitWorkExperience;
use Modules\Recruit\Http\Requests\StoreWorkExperience;

class WorkExperienceController extends AccountBaseController
{
    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        $this->workExperience = RecruitWorkExperience::all();

        return view('recruit::jobs.work-experience', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Renderable
     */
    public function store(StoreWorkExperience $request)
    {
        $workExperience = new RecruitWorkExperience;
        $workExperience->work_experience = $request->work_experience;
        $workExperience->save();

        $Experience = RecruitWorkExperience::all();

        $options = BaseModel::options($Experience, $workExperience, 'work_experience');

        return Reply::successWithData(__('recruit::messages.workExperience'), ['data' => $options]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Renderable
     */
    public function update(StoreWorkExperience $request, $id)
    {
        $workExperience = RecruitWorkExperience::findOrFail($id);
        $workExperience->work_experience = $request->work_experience;
        $workExperience->save();

        $Experience = RecruitWorkExperience::all();

        $options = BaseModel::options($Experience, null, 'work_experience');

        return Reply::successWithData(__('messages.updateSuccess'), ['data' => $options]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function destroy($id)
    {
        RecruitWorkExperience::destroy($id);

        $Experience = RecruitWorkExperience::all();

        $options = BaseModel::options($Experience, null, 'work_experience');

        return Reply::successWithData(__('messages.deleteSuccess'), ['data' => $options]);
    }
}
