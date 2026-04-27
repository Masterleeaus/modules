<?php

namespace Modules\Recruit\Http\Controllers\Front;

use App\Models\GlobalSetting;
use Carbon\Carbon;
use App\Models\Team;
use App\Models\User;
use App\Helper\Files;
use App\Helper\Reply;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\CompanyAddress;
use App\Models\Currency;
use App\Models\InvoiceSetting;
use App\Scopes\CompanyScope;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Modules\Recruit\Entities\RecruitJob;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Modules\Recruit\Entities\RecruitSetting;
use Modules\Recruit\Entities\ApplicationSource;
use Modules\Recruit\Entities\RecruitJobAddress;
use Modules\Recruit\Events\NewJobApplicationEvent;
use Modules\Recruit\Entities\RecruitJobApplication;
use Modules\Recruit\Entities\RecruitJobOfferLetter;
use Modules\Recruit\Entities\RecruitApplicationFile;
use Modules\Recruit\Entities\RecruitApplicationSkill;
use Modules\Recruit\Entities\RecruitApplicationStatus;
use Modules\Recruit\Entities\RecruitCustomQuestion;
use Modules\Recruit\Entities\RecruitFooterLink;
use Modules\Recruit\Entities\RecruitJobAlert;
use Modules\Recruit\Entities\RecruitJobCategory;
use Modules\Recruit\Entities\RecruitJobCustomAnswer;
use Modules\Recruit\Entities\RecruitJobOfferQuestion;
use Modules\Recruit\Entities\RecruitJobQuestion;
use Modules\Recruit\Entities\RecruitJobSkill;
use Modules\Recruit\Entities\RecruitJobType;
use Modules\Recruit\Entities\RecruitSalaryStructure;
use Modules\Recruit\Entities\RecruitSelectedSalaryComponent;
use Modules\Recruit\Entities\RecruitWorkExperience;
use Modules\Recruit\Notifications\OfferLetterAccept;
use Modules\Recruit\Notifications\NewJobApplication;
use Modules\Recruit\Notifications\OfferLetterReject;
use Modules\Recruit\Events\JobOfferStatusChangeEvent;
use Modules\Recruit\Http\Controllers\JobOfferLetterController;
use Modules\Recruit\Notifications\FrontJobApplyCandidate;
use Modules\Recruit\Http\Requests\Front\FrontJobApplication;
use Modules\Recruit\Http\Requests\Front\StoreJobAlertRequest;
use Modules\Recruit\Http\Requests\OfferLetter\StoreAcceptRequest;

class FrontJobController extends FrontBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('recruit::app.menu.recruit');

        $this->middleware(function ($request, $next) {

            $slug = explode('/', url()->current());
            $slug = end($slug);

            $this->company = Company::withoutGlobalScope(CompanyScope::class)
                ->where('hash', $slug)
                ->first();

            if (!is_null($this->company)) {
                $careerSite = RecruitSetting::where('company_id', $this->company->id)
                    ->select('career_site')
                    ->first();

                abort_if(!$careerSite, 404);

                if (Auth::user() && User::isAdmin(user()->id) && $careerSite->career_site != 'yes') {
                    session(['messageforAdmin' => 'admin']);
                }
                else {
                    abort_if($careerSite->career_site != 'yes', 404);
                }

                $this->customPages = RecruitFooterLink::where('company_id', '=', $this->company->id)->where('status', 'active')->get();

                App::setLocale($this->company->locale);
                setlocale(LC_TIME, $this->company->locale . '_' . strtoupper($this->company->locale));
            }

            $this->messageforAdmin = null;

            if (session()->has('messageforAdmin')) {
                $this->messageforAdmin = __('recruit::messages.careerSiteDisableMessage');
            }

            return $next($request);
        });
    }

    public function index($slug = null)
    {
        if(!$this->company || !isset($this->company->id)){
            abort(404);
        }

        $this->address = CompanyAddress::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->company->id)
            ->get();

        $this->setting = RecruitSetting::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->company->id)
            ->first();

        return view('recruit::front.index', $this->data);
    }

    public function jobOpenings($slug)
    {
        $this->company = Company::withoutGlobalScope(CompanyScope::class)->where('hash', $slug)->firstOrFail();

        $this->departments = Team::where('company_id', '=', $this->company->id)->get();
        $this->jobTypes = RecruitJobType::where('company_id', '=', $this->company->id)->get();
        $this->jobCategories = RecruitJobCategory::where('company_id', '=', $this->company->id)->get();
        $this->workExperiences = RecruitWorkExperience::where('company_id', '=', $this->company->id)->get();
        $this->setting = RecruitSetting::withoutGlobalScope(CompanyScope::class)->where('company_id', $this->company->id)->first();
        $this->jobs = RecruitJob::with('team', 'skills.skill', 'address')
            ->where('status', 'open')
            ->where('remaining_openings', '>', 0)
            ->withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->company->id)->get();

        $this->locations = RecruitJobAddress::with('jobs', 'job.team', 'job.company', 'job.jobType', 'location')->whereHas('jobs', function ($q) {
            $q->withoutGlobalScope(CompanyScope::class)
                ->where('company_id', $this->company->id)
                ->where('status', '=', 'open')
                ->where('remaining_openings', '>', 0)
                ->where(function ($query) {
                    return $query
                        ->where(DB::raw('DATE(`end_date`)'), '>=', now()->format('Y-m-d'))
                        ->orWhere('end_date', '=', null);
                });
        })->get();

        $this->firstJob = RecruitJob::with(['address', 'team', 'skills.skill'])->whereHas('address', function ($q) {
            $q->withoutGlobalScope(CompanyScope::class)
                ->where('status', '=', 'open')
                ->where('company_id', $this->company->id)
                ->where('remaining_openings', '>', 0)
                ->where(function ($query) {
                    return $query
                        ->where(DB::raw('DATE(`end_date`)'), '>=', now()->format('Y-m-d'))
                        ->orWhere('end_date', '=', null);
                });
        })->first();

        $this->department = Team::all();
        $this->companyName = $this->firstJob ? $this->company->company_name : '';

        return view('recruit::front.job-openings', $this->data);
    }

    public function jobApply($slug, $locationId, $company = null )
    {
        if(!$company){
            abort(404);
        }

        $this->globalSetting = global_setting();

        $this->companyData = Company::withoutGlobalScope(CompanyScope::class)->where('hash', $company)->firstOrFail();
        $this->setting = RecruitSetting::withoutGlobalScope(CompanyScope::class)->where('company_id', $this->companyData->id)->first();

        $this->job = RecruitJob::with(['currency', 'question', 'address'])->where('slug', $slug)->where('company_id', $this->companyData->id)->first();
        $this->skills = RecruitJobSkill::with('skill')->where('recruit_job_id', $this->job->id)->get();
        $this->recruitSetting = RecruitSetting::where('company_id', $this->companyData->id)->first();
        $this->applicationSources = ApplicationSource::where('company_id', $this->companyData->id)->get();
        $this->currency = $this->job ? Currency::where('id', '=', $this->job->currency_id)->first() : null;
        $this->location = CompanyAddress::find($locationId);
        $this->SelectedQuestions = $this->job ? RecruitJobQuestion::where('recruit_job_id', $this->job->id)->get() : null;

        $this->fields = $this->fetchQuestion($this->SelectedQuestions);
        $this->values = collect();
        $this->allQuestions = $this->job->question;

        return view('recruit::front.job-apply', $this->data);
    }

    public function jobDetail($jobId, $locationId, $company)
    {
        $this->company = Company::withoutGlobalScope(CompanyScope::class)->where('hash', $company)->firstOrFail();
        $this->setting = RecruitSetting::withoutGlobalScope(CompanyScope::class)->where('company_id', $this->company->id)->first();

        $this->job = RecruitJob::with('address', 'skills.skill', 'workExperience', 'currency')
            ->where('id', $jobId)
            ->where('status', 'open')
            ->withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->company->id)
            ->first();

        $this->jobLocation = CompanyAddress::findOrFail($locationId);

        $view = view('recruit::front.job-detail', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'data' => $this->data, 'html' => $view]);
    }

    public function jobDetailPage($jobId, $locationId, $company)
    {
        $this->company = Company::withoutGlobalScope(CompanyScope::class)->where('hash', $company)->firstOrFail();
        $this->setting = RecruitSetting::withoutGlobalScope(CompanyScope::class)->where('company_id', $this->company->id)->first();

        $this->job = RecruitJob::with('address', 'skills.skill', 'workExperience', 'team', 'category', 'subcategory', 'currency')
            ->where('slug', $jobId)
            ->where('status', 'open')
            ->withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->company->id)
            ->first();

        abort_if(!$this->job, 404);

        $this->jobLocation = CompanyAddress::findOrFail($locationId);

        return view('recruit::front.job-detail-page', $this->data);
    }

    public function fetchJob(Request $request, $company)
    {
        $this->departmentId = $request->department_id;
        $this->jobTypeId = $request->job_type_id;
        $this->jobCategoryId = $request->job_category_id;
        $this->workExperienceId = $request->work_experience_id;
        $this->remoteJob = $request->remote_job;
        $this->company = Company::withoutGlobalScope(CompanyScope::class)->where('hash', $company)->firstOrFail();

        $this->departments = Team::where('company_id', '=', $this->company->id)->get();

        $this->locations = RecruitJobAddress::with('jobs', 'job.team', 'location', 'job.jobType', 'job.company')
            ->whereHas('jobs', function ($q) {
                $q->withoutGlobalScope(CompanyScope::class)
                    ->where('company_id', $this->company->id)
                    ->where('status', '=', 'open')
                    ->where('remaining_openings', '>', 0);

                if ($this->departmentId != 'all') {
                    $q->where('department_id', $this->departmentId);
                }

                if ($this->jobTypeId != 'all') {
                    $q->where('recruit_job_type_id', $this->jobTypeId);
                }

                if ($this->jobCategoryId != 'all') {
                    $q->where('recruit_job_category_id', $this->jobCategoryId);
                }

                if ($this->workExperienceId != 'all') {
                    $q->where('recruit_work_experience_id', $this->workExperienceId);
                }

                if ($this->remoteJob != 'all') {
                    $q->where('remote_job', $this->remoteJob);
                }

                $q->where(function ($query) {
                    return $query
                        ->where(DB::raw('DATE(`end_date`)'), '>=', now()->format('Y-m-d'))
                        ->orWhere('end_date', '=', null);
                });
            })->get();

        $job_id = $this->locations[0]->job->id ?? null;

        $this->job = null;

        if ($job_id != null) {
            $location_id = $this->locations[0]->location->id;
            $this->jobLocation = CompanyAddress::findOrFail($location_id);

            $this->job = RecruitJob::with(['address', 'team', 'skills.skill', 'workExperience', 'currency'])
                ->withoutGlobalScope(CompanyScope::class)
                ->where('id', $job_id)
                ->where('company_id', $this->company->id)
                ->where('status', 'open')
                ->where('remaining_openings', '>', 0)->first();
        }

        $view = view('recruit::front.fetch-job', $this->data)->render();
        $firstjob = view('recruit::front.fetch-first-job', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'data' => $this->data, 'html' => $view, 'firstjob' => $firstjob]);
    }

    public function jobOfferLetterStatusChange(StoreAcceptRequest $request, $id)
    {
        $jobOffer = RecruitJobOfferLetter::with('jobApplication', 'job')->findOrFail($id);

        if ($request->status == 'accept') {

            if ($jobOffer->sign_require == 'on') {
                $image = $request->signature;  // your base64 encoded
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = str_random(32) . '.' . 'jpg';

                Files::createDirectoryIfNotExist('offer/accept');

                File::put(public_path() . '/' . Files::UPLOAD_FOLDER . '/offer/accept/' . $imageName, base64_decode($image));

                $jobOffer->sign_image = $imageName;
            }

            Notification::send($jobOffer->jobApplication, new OfferLetterAccept($jobOffer->job, $jobOffer->jobApplication));

        }
        elseif ($request->status == 'decline') {
            $jobOffer->decline_reason = $request->reason;

            Notification::send($jobOffer->jobApplication, new OfferLetterReject($jobOffer->job, $jobOffer->jobApplication));
        }

        $jobOffer->ip_address = request()->ip();
        $jobOffer->offer_accept_at = now();
        $jobOffer->status = $request->status;
        $jobOffer->save();

        if ($request->status == 'accept') {
            if (!empty($request->answer)) {

                foreach ($request->answer as $key => $value) {
                    $fieldType = RecruitCustomQuestion::findOrFail($key)->type;

                    if ($value) {
                        $value = ($fieldType == 'date') ? Carbon::createFromFormat($jobOffer->company->date_format, $value)->format('Y-m-d') : $value;
                    }

                    $answer = new RecruitJobCustomAnswer();
                    $answer->recruit_job_offer_letter_id = $jobOffer->id;
                    $answer->recruit_job_application_id = $jobOffer->jobApplication->id;
                    $answer->recruit_job_id = $jobOffer->job->id;
                    $answer->recruit_job_question_id = $key;

                    if ($request->hasFile('answer.' . $key)) {
                        Files::deleteFile($answer->filename, RecruitJobCustomAnswer::FILE_PATH);
                        $filename = Files::uploadLocalOrS3($value, RecruitJobCustomAnswer::FILE_PATH . '/' . $key);
                        $answer->filename = $value->getClientOriginalName();
                        $answer->hashname = $filename;
                        $answer->answer = null;
                    }
                    else {
                        $answer->answer = $value;
                    }

                    $answer->save();
                }
            }
        }

        event(new JobOfferStatusChangeEvent($jobOffer));

        return Reply::dataOnly(['status' => 'success']);
    }

    public function jobOfferLetter($hash)
    {
        $this->jobOffer = RecruitJobOfferLetter::with('job', 'job.question')->where('hash', $hash)->firstOrFail();
        $this->company = Company::withoutGlobalScope(CompanyScope::class)->where('id', $this->jobOffer->company_id)->firstOrFail();
        $this->currency = Currency::where('id', '=', $this->jobOffer->job->currency_id)->first();
        $this->InvoiceSetting = InvoiceSetting::where('company_id', $this->company->id)->firstOrFail();
        $this->settings = $this->company;

        $this->job_not_expired = $this->jobOffer->job_expire->gte(now());

        $this->allQuestions = $this->jobOffer->question;
        $this->label_class = '';
        $this->msg = '';

        if (!$this->job_not_expired) {
            $this->label_class = 'badge badge-dark f-15';
            $this->msg = 'Expired';
        }
        elseif ($this->jobOffer->status == 'accept') {
            $this->label_class = 'badge badge-success f-15';
            $this->msg = 'Accepted';
        }
        elseif ($this->jobOffer->status == 'decline') {
            $this->label_class = 'badge badge-danger f-15';
            $this->msg = 'Declined';
        }
        elseif ($this->jobOffer->status == 'withdraw') {
            $this->label_class = 'badge badge-info f-15';
            $this->msg = 'Withdrawn';
        }
        elseif ($this->jobOffer->status == 'pending') {
            $this->label_class = 'badge badge-warning f-15';
            $this->msg = 'Pending';
        }

        $this->SelectedQuestions = $this->jobOffer ? RecruitJobOfferQuestion::where('recruit_job_offer_letter_id', $this->jobOffer->id)->get() : null;

        $this->fields = $this->fetchQuestion($this->SelectedQuestions);
        $this->values = collect();
        $this->allQuestions = $this->jobOffer->question;

        $this->salaryStructure = RecruitSalaryStructure::where('recruit_job_offer_letter_id', $this->jobOffer->id)->first() ?? null;

        if (!is_null($this->salaryStructure)) {
            $this->selectedEarningsComponent = RecruitSelectedSalaryComponent::where('rss_id', $this->salaryStructure->id)->where('component_type', 'earning')->get();
            $this->selectedDeductionsComponent = RecruitSelectedSalaryComponent::where('rss_id', $this->salaryStructure->id)->where('component_type', 'deduction')->get();
            $earn = JobOfferLetterController::totalEarnings($this->salaryStructure);
            $deduction = JobOfferLetterController::totalDeductions($this->salaryStructure);
            $total = $this->salaryStructure->basic_salary + $earn;
            $this->fixedAllowance = $this->salaryStructure->amount - $total;
            $this->grossSalary = $this->fixedAllowance + $total;
            $this->totalDeduction = $deduction;
            $this->netSalary = $this->grossSalary - ($this->totalDeduction);
        }

        return view('recruit::jobs.offer-letter-preview', $this->data);
    }

    /**
     * @param FrontJobApplication $request
     * @return mixed
     */
    public function saveApplication(FrontJobApplication $request)
    {
        $slug = $request->companyHash;
        $this->company = Company::withoutGlobalScope(CompanyScope::class)->where('hash', $slug)->firstOrFail();
        $recruitSetting = RecruitSetting::where('company_id', $this->company->id)->select('google_recaptcha_status')->first();

        if ($recruitSetting->google_recaptcha_status == 'active') {

            if (global_setting()->google_recaptcha_status == 'active') {
                $gRecaptchaResponseInput = 'g_recaptcha';
                $gRecaptchaResponse = $request->{$gRecaptchaResponseInput};
                $validateRecaptcha = GlobalSetting::validateGoogleRecaptcha($gRecaptchaResponse);

                if (!$validateRecaptcha) {
                    return Reply::error(__('auth.recaptchaFailed'));
                }
            }
        }

        $status = RecruitApplicationStatus::where('company_id', '=', $this->company->id)->where('slug', 'applied')->first();
        $jobApplication = new RecruitJobApplication();
        $jobApplication->company_id = $this->company->id;
        $jobApplication->full_name = $request->full_name;
        $jobApplication->recruit_job_id = $request->job_id;
        $jobApplication->location_id = $request->location_id;
        $jobApplication->recruit_application_status_id = $status->id;
        $jobApplication->email = $request->email;
        $jobApplication->application_source_id = $request->source;
        $jobApplication->phone = $request->phone;
        $jobApplication->application_sources = 'careerWebsite';
        $jobApplication->cover_letter = $request->cover_letter;
        $jobApplication->column_priority = 0;
        $jobApplication->total_experience = $request->total_experience;
        $jobApplication->current_location = $request->current_location;
        $jobApplication->current_ctc = $request->current_ctc;
        $jobApplication->currenct_ctc_rate = $request->currenct_ctc_rate;
        $jobApplication->expected_ctc = $request->expected_ctc;
        $jobApplication->expected_ctc_rate = $request->expected_ctc_rate;
        $jobApplication->notice_period = $request->notice_period;

        if ($request->resume) {
            $jobApplication->resume = $request->resume;
        }

        if ($request->hasFile('photo')) {
            Files::deleteFile($jobApplication->photo, 'avatar');
            $jobApplication->photo = Files::uploadLocalOrS3($request->photo, 'avatar', 300);
        }

        if ($request->has('gender')) {
            $jobApplication->gender = $request->gender;
        }

        if ($request->date_of_birth != null) {
            if ($request->has('date_of_birth')) {
                $jobApplication->date_of_birth = $request->date_of_birth ? Carbon::createFromFormat($this->company->date_format, $request->date_of_birth)->format('Y-m-d') : null;
            }
        }

        $jobApplication->save();

        $job = RecruitJobApplication::with('job')->where('id', $request->job_id)->get();
        // Mail to candidate
        Notification::send($jobApplication, new FrontJobApplyCandidate($jobApplication, $job));

        if (request()->hasFile('resume')) {
            Files::deleteFile($jobApplication->resume, 'application-files/' . $jobApplication->id);

            $file = new RecruitApplicationFile();
            $file->recruit_job_application_id = $jobApplication->id;
            $file->filename = request()->resume->getClientOriginalName();
            $file->hashname = Files::uploadLocalOrS3(request()->resume, 'application-files/' . $jobApplication->id);
            $file->size = request()->resume->getSize();
            $file->save();
        }

        if ($request->has('gender')) {
            $jobApplication->gender = $request->gender;
        }

        if ($request->has('dob')) {
            $jobApplication->dob = $request->dob;
        }

        if (!empty($request->skill_id)) {
            RecruitApplicationSkill::where('recruit_job_application_id', $request->application_id)->delete();

            foreach ($request->skill_id as $tag) {
                $jobSkill = new RecruitApplicationSkill();
                $jobSkill->recruit_job_application_id = $jobApplication->id;
                $jobSkill->recruit_skill_id = $tag;
                $jobSkill->save();
            }
        }

        if (!empty($request->answer)) {

            foreach ($request->answer as $key => $value) {
                $fieldType = RecruitCustomQuestion::findOrFail($key)->type;

                if ($value) {
                    $value = ($fieldType == 'date') ? Carbon::createFromFormat($this->company->date_format, $value)->format('Y-m-d') : $value;
                }

                $answer = new RecruitJobCustomAnswer();
                $answer->recruit_job_application_id = $jobApplication->id;
                $answer->recruit_job_id = $request->job_id;
                $answer->recruit_job_question_id = $key;
                $answer->answer = $value;

                if ($request->hasFile('answer.' . $key)) {
                    Files::deleteFile($answer->filename, RecruitJobCustomAnswer::FILE_PATH . '/' . $key);
                    $answer->filename = $value->getClientOriginalName();
                    $answer->hashname = Files::uploadLocalOrS3($value, RecruitJobCustomAnswer::FILE_PATH . '/' . $key);
                    $answer->answer = null;
                }

                $answer->save();
            }
        }


        return Reply::dataOnly(['status' => 'success', 'redirectUrl' => route('front.thankyou-page', $slug), 'application_id' => $jobApplication->id]);
    }

    public function thankyouPage($slug)
    {
        $this->company = Company::withoutGlobalScope(CompanyScope::class)->where('hash', $slug)->firstOrFail();

        $this->address = CompanyAddress::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->company->id)
            ->get();

        $this->setting = RecruitSetting::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->company->id)
            ->first();

        return view('recruit::front.thankyou-page', $this->data);
    }

    public function customPage($job, $slug)
    {
        $this->company = Company::withoutGlobalScope(CompanyScope::class)->where('hash', $slug)->firstOrFail();
        $this->customPage = RecruitFooterLink::where('slug', $job)->where('status', 'active')->first();

        abort_if(is_null($this->customPage), 404);

        $this->setting = RecruitSetting::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->company->id)
            ->first();

        $this->pageTitle = $this->customPage->title;

        return view('recruit::front.custom-page', $this->data);
    }

    public function jobDetailsModal(Request $request)
    {
        $this->company = Company::withoutGlobalScope(CompanyScope::class)->where('hash', $request->slug)->firstOrFail();
        $this->setting = RecruitSetting::withoutGlobalScope(CompanyScope::class)->where('company_id', $this->company->id)->first();

        $this->job = RecruitJob::with('address', 'skills.skill', 'workExperience', 'currency')
            ->where('id', $request->id)
            ->where('status', 'open')
            ->withoutGlobalScope(CompanyScope::class)->where('company_id', $this->company->id)
            ->first();

        $this->jobLocation = CompanyAddress::findOrFail($request->locationId);

        return view('recruit::front.job-details-modal', $this->data);
    }

    public function jobAlert($slug)
    {
        $this->company = Company::withoutGlobalScope(CompanyScope::class)->where('hash', $slug)->firstOrFail();
        $this->categories = RecruitJobCategory::where('company_id', $this->company->id)->get();
        $this->locations = CompanyAddress::where('company_id', $this->company->id)->get();
        $this->workExperiences = RecruitWorkExperience::where('company_id', $this->company->id)->get();
        $this->jobTypes = RecruitJobType::where('company_id', $this->company->id)->get();

        return view('recruit::front.job-alert', $this->data);
    }

    public function jobAlertStore(StoreJobAlertRequest $request)
    {
        $this->company = Company::withoutGlobalScope(CompanyScope::class)->where('hash', $request->slug)->firstOrFail();

        $alert = new RecruitJobAlert();
        $alert->company_id = $this->company->id;
        $alert->email = $request->email;
        $alert->recruit_job_category_id = $request->job_category;
        $alert->location_id = $request->location;
        $alert->recruit_work_experience_id = $request->work_experience;
        $alert->recruit_job_type_id = $request->job_type;
        $alert->status = 'active';
        $alert->hashname = str_random(16);
        $alert->save();

        return Reply::success(__('recruit::messages.jobAlertCreated'));
    }

    public function jobAlertUnsubscribe($slug, $alertHash)
    {
        $this->company = Company::withoutGlobalScope(CompanyScope::class)->where('hash', $slug)->firstOrFail();

        $this->setting = RecruitSetting::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->company->id)
            ->first();

        $unsubscribe = RecruitJobAlert::where('hashname', $alertHash)->firstOrFail();
        $unsubscribe->status = 'inactive';
        $unsubscribe->save();

        $this->customPage = null;

        return view('recruit::front.custom-page', $this->data);
    }

    public function download($id, $slug)
    {
        $this->company = Company::withoutGlobalScope(CompanyScope::class)->where('hash', $slug)->first();
        $this->jobOffer = RecruitJobOfferLetter::with(['files', 'job', 'jobApplication'])->findOrfail($id);
        $this->currency = Currency::where('id', '=', $this->jobOffer->job->currency_id)->first();

        $this->salaryStructure = RecruitSalaryStructure::where('recruit_job_offer_letter_id', $id)->first() ?? null;

        if (!is_null($this->salaryStructure)) {
            $this->selectedEarningsComponent = RecruitSelectedSalaryComponent::where('rss_id', $this->salaryStructure->id)->where('component_type', 'earning')->get();
            $this->selectedDeductionsComponent = RecruitSelectedSalaryComponent::where('rss_id', $this->salaryStructure->id)->where('component_type', 'deduction')->get();
            $earn = JobOfferLetterController::totalEarnings($this->salaryStructure);
            $deduction = JobOfferLetterController::totalDeductions($this->salaryStructure);
            $total = $this->salaryStructure->basic_salary + $earn;
            $this->fixedAllowance = $this->salaryStructure->amount - $total;
            $this->grossSalary = $this->fixedAllowance + $total;
            $this->totalDeduction = $deduction;
            $this->netSalary = $this->grossSalary - ($this->totalDeduction);
        }

        $pdf = app('dompdf.wrapper');
        $pdf->setOption('enable_php', true);
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        $this->global = $this->settings = global_setting();

        $pdf->loadView('recruit::jobs.offer-letter.offer-letter-pdf', $this->data);

        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $canvas->page_text(530, 820, 'Page {PAGE_NUM} of {PAGE_COUNT}', null, 10, array(0, 0, 0));

        $filename = 'offer-letter-' . $this->jobOffer->jobApplication->full_name;

        return $pdf->download($filename . '.pdf');
    }

    public function fetchQuestion($selectedQuestions)
    {
        $fields = [];

        if (is_null($selectedQuestions)) {
            return $fields;
        }

        foreach ($selectedQuestions as $group) {
            $customFields = RecruitCustomQuestion::where('id', $group->recruit_custom_question_id)->get();
            $customFields = collect($customFields);
            // convert values to json array if type is select
            $customFields = $customFields->map(function ($item) {
                if ($item->type == 'select' || $item->type == 'radio' || $item->type == 'checkbox') {
                    $item->values = json_decode($item->values);

                    return $item;
                }

                return $item;
            });

            $fields[] = $customFields;
        }

        return $fields;
    }

    public function acceptOffer($id)
    {
        $this->jobOffer = RecruitJobOfferLetter::with('job', 'job.question')->where('id', $id)->firstOrFail();
        $this->company = Company::withoutGlobalScope(CompanyScope::class)->where('id', $this->jobOffer->company_id)->firstOrFail();

        $this->SelectedQuestions = $this->jobOffer ? RecruitJobOfferQuestion::where('recruit_job_offer_letter_id', $this->jobOffer->id)->get() : null;

        $this->fields = $this->fetchQuestion($this->SelectedQuestions);
        $this->values = collect();
        $this->allQuestions = $this->jobOffer->question;

        return view('recruit::jobs.offer-letter.accept-job-offer', $this->data);
    }

}
