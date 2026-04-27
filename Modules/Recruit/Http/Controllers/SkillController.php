<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use Modules\Recruit\DataTables\SkillDataTable;
use App\Http\Controllers\AccountBaseController;
use Modules\Recruit\Entities\RecruitSetting;
use Modules\Recruit\Entities\RecruitSkill;
use Modules\Recruit\Http\Requests\Skill\StoreJobSkill;
use Modules\Recruit\Http\Requests\Skill\StoreSkill;
use Modules\Recruit\Http\Requests\Skill\UpdateJobSkill;
use Modules\Recruit\Http\Requests\Skill\UpdateSkill;

class SkillController extends AccountBaseController
{

    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('recruit::app.menu.skill');
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(RecruitSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    public function index(SkillDataTable $dataTable)
    {
        $permission = user()->permission('manage_skill');
        abort_403(!in_array($permission, ['all']));

        return $dataTable->render('recruit::skills.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $this->pageTitle = __('app.add') . ' ' . __('app.skills');

        $permission = user()->permission('manage_skill');
        abort_403(!in_array($permission, ['all']));

        if (request()->ajax()) {
            $html = view('recruit::skills.ajax.create', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'recruit::skills.ajax.create';

        return view('recruit::skills.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(StoreSkill $request)
    {
        $allSkills = RecruitSkill::where('company_id', '=', company()->id)->pluck('name')->toArray();

        if (array_intersect($allSkills, $request->names) != null){
            $this->validate($request, [
                'names.*' => 'unique:recruit_skills,name'
            ]);
        }
        else{
            $skills = $request->names;

            if (count($skills) > 1) {
                $unique = array_unique($skills);
                $duplicates = array_diff_assoc($skills, $unique);

                if (!empty($duplicates)) {
                    return Reply::error(__('recruit::modules.message.skillSame'));
                }
            }

            foreach ($skills as $skill) {
                if (!is_null($skill)) {
                    RecruitSkill::create(['name' => $skill]);
                }
            }

            return Reply::successWithData(__('recruit::modules.message.skillAdded'), ['redirectUrl' => route('job-skills.index')]);
        }

    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->skills = RecruitSkill::findOrFail($id);

        $this->permission = user()->permission('manage_skill');
        abort_403(!($this->permission == 'all'));

        if (request()->ajax()) {
            $html = view('recruit::skills.ajax.edit', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'recruit::skills.ajax.edit';

        return view('recruit::skills.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(UpdateSkill $request, $id)
    {
        $skills = RecruitSkill::findOrFail($id);

        $skills->name = $request->name;
        $skills->save();

        return Reply::successWithData(__('recruit::modules.message.updateSuccess'), ['redirectUrl' => route('job-skills.index')]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        RecruitSkill::destroy($id);
        $this->permission = user()->permission('manage_skill');
        abort_403(!($this->permission == 'all'));

        return Reply::successWithData(__('recruit::modules.message.deleteSuccess'), ['redirectUrl' => route('job-skills.index')]);
    }

    public function applyQuickAction(Request $request)
    {
        if ($request->action_type === 'delete') {
            $this->deleteRecords($request);

            return Reply::success(__('recruit::modules.message.deleteSuccess'));
        }

        return Reply::error(__('messages.selectAction'));
    }

    protected function deleteRecords($request)
    {
        abort_403(user()->permission('manage_skill') != 'all');

        RecruitSkill::whereIn('id', explode(',', $request->row_ids))->delete();
    }

    public function addSkill(Request $request)
    {
        $this->permission = user()->permission('manage_skill');
        abort_403(!($this->permission == 'all'));

        $this->selectedSkills = $request->skill;
        $this->skills = RecruitSkill::all();

        return view('recruit::jobs.skills.create', $this->data);
    }

    public function storeSkill(StoreJobSkill $request)
    {
        $group = new RecruitSkill();
        $group->name = $request->names;
        $group->save();

        if ($request->selectedSkills != '') {
            $selectedSkills = $request->selectedSkills . ',' . $group->id;
            $selectedSkills = explode(',', $selectedSkills);
        }
        else {
            $selectedSkills = [$group->id];
        }

        $skills = RecruitSkill::all();

        $options = '';

        foreach ($skills as $skill) {
            $checkSelected = '';

            if (in_array($skill->id, $selectedSkills)) {
                $checkSelected = 'selected';
            }

            $options .= '<option ' . $checkSelected . '
            data-content="<span class=\'badge badge-pill badge-light border\'><div class=\'d-inline-block mr-1\'></div> ' . $skill->name . ' </span>"
            value="' . $skill->id . '"> ' . $skill->name . '</option>';
        }

        return Reply::successWithData(__('recruit::modules.message.skillAdded'), ['data' => $options]);
    }

    public function updateSkill(UpdateJobSkill $request, $id)
    {
        $group = RecruitSkill::findOrFail($id);
        $group->name = strip_tags($request->name);
        $group->save();

        if ($request->selectedSkills != '') {
            $selectedSkills = $request->selectedSkills . ',' . $group->id;
            $selectedSkills = explode(',', $selectedSkills);
        }
        else {
            $selectedSkills = [$group->id];
        }

        $skills = RecruitSkill::all();
        $options = '';

        foreach ($skills as $skill) {
            $checkSelected = '';

            if (in_array($skill->id, $selectedSkills)) {
                $checkSelected = 'selected';
            }

            $options .= '<option ' . $checkSelected . '
            data-content="<span class=\'badge badge-pill badge-light border\'><div class=\'d-inline-block mr-1\'></div> ' . $skill->name . ' </span>"
            value="' . $skill->id . '"> ' . $skill->name . '</option>';
        }

        return Reply::successWithData(__('messages.updateSuccess'), ['data' => $options]);
    }

}
