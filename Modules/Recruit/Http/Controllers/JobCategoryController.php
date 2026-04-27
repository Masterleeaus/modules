<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Recruit\Entities\RecruitJobCategory;
use Modules\Recruit\Http\Requests\StoreJobCategory;

class JobCategoryController extends AccountBaseController
{
    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        $this->categories = RecruitJobCategory::all();
        $this->deletePermission = user()->permission('manage_job_category');

        return view('recruit::jobs.job-category.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Renderable
     */
    public function store(StoreJobCategory $request)
    {
        $new = new RecruitJobCategory;
        $new->category_name = strip_tags($request->category_name);
        $new->save();

        $category = RecruitJobCategory::all();

        return Reply::successWithData(__('messages.recordSaved'), ['data' => $category]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Renderable
     */
    public function update(StoreJobCategory $request, $id)
    {
        $this->editPermission = user()->permission('manage_job_category');
        abort_403($this->editPermission != 'all');

        $newCategory = RecruitJobCategory::find($id);
        $newCategory->category_name = strip_tags($request->category_name);
        $newCategory->save();

        $category = RecruitJobCategory::all();

        return Reply::successWithData(__('messages.updateSuccess'), ['data' => $category]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $this->deletePermission = user()->permission('manage_job_category');
        abort_403($this->deletePermission != 'all');

        RecruitJobCategory::destroy($id);

        $category = RecruitJobCategory::all();

        return Reply::successWithData(__('messages.deleteSuccess'), ['data' => $category]);
    }
}
