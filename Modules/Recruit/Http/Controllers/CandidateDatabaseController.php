<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Models\CompanyAddress;
use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Entities\RecruitSetting;
use Modules\Recruit\Entities\RecruitSkill;
use Illuminate\Contracts\Support\Renderable;
use Modules\Recruit\Entities\RecruitJobSkill;
use App\Http\Controllers\AccountBaseController;
use App\Models\Currency;
use Modules\Recruit\Entities\RecruitJobApplication;
use Modules\Recruit\Entities\RecruitCandidateDatabase;
use Modules\Recruit\DataTables\CandidateDatabaseDataTable;
use Modules\Recruit\Entities\RecruitApplicationSkill;

class CandidateDatabaseController extends AccountBaseController
{

    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('recruit::app.menu.candidatedatabase');
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(RecruitSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    public function index(CandidateDatabaseDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_job_application');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned']));

        $this->jobs = RecruitJob::all();
        $this->locations = CompanyAddress::all();
        $this->skills = RecruitSkill::all();
        $this->names = RecruitCandidateDatabase::get(['name', 'id']);

        return $dataTable->render('recruit::candidate-database.index', $this->data);
    }

    public function store(Request $request)
    {
        $application = RecruitJobApplication::with('job')->find($request->row_id);
        $skillsdata = RecruitApplicationSkill::where('recruit_job_application_id', $application->id)->get('recruit_skill_id');

        $applicant_skills = array();

        foreach ($skillsdata as $skills) {
            $applicant_skill = RecruitSkill::where('id', $skills->recruit_skill_id)->select('id')->get();
            $applicant_skills[] = $applicant_skill[0]['id'];
        }

        $jobArchive = new RecruitCandidateDatabase();
        $jobArchive->name = $application->full_name;
        $jobArchive->recruit_job_id = $application->job->id;
        $jobArchive->location_id = $application->location_id;
        $jobArchive->job_applied_on = $application->created_at;
        $jobArchive->skills = $applicant_skills;
        $jobArchive->job_application_id = $request->row_id;
        $jobArchive->save();

        RecruitJobApplication::destroy($request->row_id);
        $redirectUrl = route('job-appboard.index');

        return Reply::successWithData(__('recruit::messages.archiveSuccess'), ['redirectUrl' => $redirectUrl]);
    }

    public function show($id)
    {

        $viewPermission = user()->permission('view_job_application');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned']));

        $this->database = RecruitCandidateDatabase::with('job', 'job.address')->findOrfail($id);

        $this->application = RecruitJobApplication::with('job', 'job.address')->where('id', $this->database->job_application_id)->where('full_name', $this->database->name)->withTrashed()->first() ?? null;
        $this->address = CompanyAddress::where('id', $this->database->location_id)->first();

        $this->skills = RecruitSkill::whereIn('id', $this->database->skills)->select('name')->get();
        $this->currency = Currency::where('id', '=', $this->application->job->currency_id)->first();

        if (request()->ajax()) {
            if (request('json') == true) {
                $html = view('recruit::candidate-database.ajax.show', $this->data)->render();

                return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
            }

            $html = view('recruit::candidate-database.ajax.show', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'recruit::candidate-database.ajax.show';

        return view('recruit::candidate-database.show', $this->data);
    }

    public function update(Request $request, $id)
    {
        $restoreAccount = RecruitJobApplication::withTrashed()->find($request->job_app_id);
        $restoreAccount->deleted_at = null;
        $restoreAccount->save();

        RecruitCandidateDatabase::destroy($id);

        return Reply::successWithData(__('recruit::messages.retriveSuccess'), ['status' => 'success']);
    }

}
