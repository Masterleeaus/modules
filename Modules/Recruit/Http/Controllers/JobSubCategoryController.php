<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Recruit\Entities\RecruitJobCategory;
use Modules\Recruit\Entities\RecruitJobSubCategory;
use Modules\Recruit\Http\Requests\StoreJobSubCategory;

class JobSubCategoryController extends AccountBaseController
{
    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        $this->subcategories = RecruitJobSubCategory::with('category')->get();
        $this->categories = RecruitJobCategory::all();
        $this->deletePermission = user()->permission('manage_job_sub_category');

        return view('recruit::jobs.job-sub-category.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Renderable
     */
    public function store(StoreJobSubCategory $request)
    {
        $category = new RecruitJobSubCategory;
        $category->recruit_job_category_id = $request->recruit_job_category_id;
        $category->sub_category_name = $request->sub_category_name;
        $category->save();
        $categories = RecruitJobCategory::all();

        return Reply::successWithData(__('messages.recordSaved'), ['data' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Renderable
     */
    public function update(StoreJobSubCategory $request, $id)
    {
        abort_403(user()->permission('manage_job_sub_category') != 'all');

        $category = RecruitJobSubCategory::findOrFail($id);

        $category->sub_category_name = strip_tags($request->sub_category_name);
        $category->save();

        $categoryData = RecruitJobSubCategory::all();

        return Reply::successWithData(__('messages.updateSuccess'), ['data' => $categoryData]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function destroy($id)
    {
        abort_403(user()->permission('manage_job_sub_category') != 'all');

        RecruitJobSubCategory::findOrFail($id);

        RecruitJobSubCategory::destroy($id);
        $categoryData = RecruitJobSubCategory::all();

        return Reply::successWithData(__('messages.deleteSuccess'), ['data' => $categoryData]);
    }

    public function getSubCategories($id)
    {
        $sub_categories = RecruitJobSubCategory::where('recruit_job_category_id', $id)->get();

        return Reply::dataOnly(['status' => 'success', 'data' => $sub_categories]);
    }
}
