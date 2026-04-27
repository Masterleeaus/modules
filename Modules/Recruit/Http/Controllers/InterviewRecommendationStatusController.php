<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Models\BaseModel;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Recruit\Entities\RecruitInterviewEvaluation;
use Modules\Recruit\Entities\RecruitRecommendationStatus;
use Modules\Recruit\Http\Requests\RecommendationStatus\StoreStatus;
use Modules\Recruit\Http\Requests\RecommendationStatus\UpdateStatus;

class InterviewRecommendationStatusController extends AccountBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index()
    {
        return view('recruit::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        $addPermission = user()->permission('add_recommendation_status');
        abort_403(! in_array($addPermission, ['all']));

        $this->statuses = RecruitRecommendationStatus::where('company_id', '=', company()->id)->get();

        return view('recruit::interview-schedule.recommendation-status.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Renderable
     */
    public function store(StoreStatus $request)
    {
        $addPermission = user()->permission('add_recommendation_status');
        abort_403(! in_array($addPermission, ['all']));

        $group = new RecruitRecommendationStatus;
        $group->status = $request->status;
        $group->save();

        $statuses = RecruitRecommendationStatus::where('company_id', '=', company()->id)->get();

        $options = BaseModel::options($statuses, $group, 'status');

        return Reply::successWithData(__('recruit::messages.statusAdded'), ['data' => $options]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Renderable
     */
    public function update(UpdateStatus $request, $id)
    {
        $editPermission = user()->permission('edit_recommendation_status');
        abort_403(! in_array($editPermission, ['all']));

        $group = RecruitRecommendationStatus::findOrFail($id);
        $group->status = strip_tags($request->status);
        $group->save();

        $statuses = RecruitRecommendationStatus::where('company_id', '=', company()->id)->get();
        $options = BaseModel::options($statuses, null, 'status');

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
        $deletePermission = user()->permission('delete_recommendation_status');
        abort_403(! in_array($deletePermission, ['all']));

        RecruitInterviewEvaluation::where('recruit_recommendation_status_id', $id)->update(['recruit_recommendation_status_id' => null]);
        RecruitRecommendationStatus::destroy($id);

        $statuses = RecruitRecommendationStatus::all();
        $options = BaseModel::options($statuses, null, 'status');

        return Reply::successWithData(__('messages.deleteSuccess'), ['data' => $options]);
    }
}
