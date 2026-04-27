<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\Admin\Employee\StoreRequest;
use App\Models\Currency;
use App\Models\Designation;
use App\Models\EmployeeDetails;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\UserAuth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Payroll\Entities\SalaryComponent;
use Modules\Payroll\Http\Controllers\EmployeeMonthlySalaryController;
use Modules\Recruit\DataTables\JobOfferLetterDataTable;
use Modules\Recruit\Entities\OfferLetterHistory;
use Modules\Recruit\Entities\RecruitCustomQuestion;
use Modules\Recruit\Entities\RecruitJob;
use Modules\Recruit\Entities\RecruitJobApplication;
use Modules\Recruit\Entities\RecruitJobCustomAnswer;
use Modules\Recruit\Entities\RecruitJobOfferLetter;
use Modules\Recruit\Entities\RecruitJobOfferQuestion;
use Modules\Recruit\Entities\RecruitSalaryStructure;
use Modules\Recruit\Entities\RecruitSelectedSalaryComponent;
use Modules\Recruit\Entities\RecruitSetting;
use Modules\Recruit\Events\OfferLetterEvent;
use Modules\Recruit\Http\Requests\OfferLetter\StoreOfferLetter;
use App\Http\Requests\Designation\StoreRequest as DesignationStore;
use App\Models\BaseModel;
use Modules\Recruit\Http\Requests\EmployeeStoreRequest;
use Symfony\Component\Mailer\Exception\TransportException;

class JobOfferLetterController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('recruit::app.menu.offerletter');
        $this->middleware(function ($request, $next) {
            abort_403(! in_array(RecruitSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    public function index(JobOfferLetterDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_offer_letter');
        abort_403(! in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        $this->jobs = RecruitJob::all();
        $this->departments = Team::all();

        return $dataTable->render('recruit::jobs.ajax.offer-letter', $this->data);
    }

    public function create()
    {
        $addPermission = user()->permission('add_offer_letter');
        abort_403(! in_array($addPermission, ['all', 'added']));
        $this->jobId = request()->id;
        $this->pageTitle = __('recruit::modules.joboffer.addjoboffer');

        $this->jobs = RecruitJob::where('company_id', '=', company()->id)->get();
        $this->jobApplications = RecruitJobApplication::where('recruit_job_id', request()->id)->get();
        $this->applications = RecruitJobApplication::where('company_id', '=', company()->id)->get();
        $this->jobOffer = RecruitJob::where('id', request()->id)->first();
        $this->currency = $this->jobOffer ? Currency::where('id', '=', $this->jobOffer->currency_id)->first() : null;
        $this->questions = RecruitCustomQuestion::where('status', 'enable')->where('category', 'job_offer')->where('company_id', '=', company()->id)->get();

        if (in_array('Payroll', worksuite_plugins())) {
            $this->earningComponents = SalaryComponent::where('component_type', 'earning')->get();
            $this->deductionComponents = SalaryComponent::where('component_type', 'deduction')->get();
            $this->payrollController = new EmployeeMonthlySalaryController;
            $this->deductionCount = 0;
        }

        if (request()->ajax()) {
            $html = view('recruit::jobs.ajax.createOfferLetter', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'recruit::jobs.ajax.createOfferLetter';

        return view('recruit::jobs.createOfferLetter', $this->data);
    }

    public function show($id)
    {
        $this->jobOffer = RecruitJobOfferLetter::with('job')->findOrFail($id);
        $this->viewPermission = user()->permission('view_offer_letter');

        abort_403(! ($this->viewPermission == 'all'
            || ($this->viewPermission == 'added' && $this->jobOffer->added_by == user()->id)
            || ($this->viewPermission == 'owned' && user()->id == $this->jobOffer->job->recruiter_id)
            || ($this->viewPermission == 'both' && user()->id == $this->jobOffer->job->recruiter_id)
            || $this->jobOffer->added_by == user()->id));

        $this->jobs = RecruitJob::all();
        $this->applications = RecruitJobApplication::all();
        $this->salaryStructure = RecruitSalaryStructure::where('recruit_job_offer_letter_id', $id)->first() ?? null;

        if (! is_null($this->salaryStructure)) {
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

        $this->currency = Currency::where('id', '=', $this->jobOffer->job->currency_id)->first();
        $this->settings = company();

        if ($this->jobOffer->job_expire->gte(now())) {
            $this->job_not_expired = true;
        }
        else {

            $this->job_not_expired = false;
        }


        $this->label_class = '';
        $this->msg = '';

        if ($this->jobOffer->status == 'expired') {
            $this->label_class = 'badge badge-dark f-14';
            $this->msg = 'Expired';

        } elseif ($this->jobOffer->status == 'accept') {
            $this->label_class = 'badge badge-success f-14';
            $this->msg = 'Accepted';

        } elseif ($this->jobOffer->status == 'decline') {
            $this->label_class = 'badge badge-danger f-14';
            $this->msg = 'Declined';

        } elseif ($this->jobOffer->status == 'withdraw') {
            $this->label_class = 'badge badge-info f-14';
            $this->msg = 'Withdrawn';

        } elseif ($this->jobOffer->status == 'pending') {
            $this->label_class = 'badge badge-warning f-14';
            $this->msg = 'Pending';
        }

        $tab = request('tab');
        $this->activeTab = $tab ?: 'letter';

        switch ($tab) {
        case 'custom':
            $this->allAnswers = RecruitJobCustomAnswer::where('recruit_job_offer_letter_id', $id)->get();
            $this->view = 'recruit::jobs.ajax.custom-question';
            break;
        case 'history':
            $this->activity = OfferLetterHistory::where('recruit_job_offer_letter_id', $id)->orderByDesc('updated_at')->get();
            $this->view = 'recruit::jobs.ajax.history';
            break;
        default:
            $this->view = 'recruit::jobs.ajax.showOfferLetter';
            break;
        }

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('recruit::jobs.offer-letter.show', $this->data);
    }

    public static function totalEarnings($salaryStructure)
    {
        $salaryJson = json_decode($salaryStructure->salary_json, true);
        $earnings = $salaryJson['earnings'];
        $earn = [];

        foreach ($earnings as $key => $value) {
            $earn[] = $key;
        }

        $earn = array_sum($earn);

        return $earn;
    }

    public static function totalDeductions($salaryStructure)
    {
        $salaryJson = json_decode($salaryStructure->salary_json, true);
        $deductions = $salaryJson['deductions'];
        $deduction = [];

        foreach ($deductions as $key => $value) {
            $deduction[] = $key;
        }

        $deduction = array_sum($deduction);

        return $deduction;
    }

    public function store(StoreOfferLetter $request)
    {
        $addPermission = user()->permission('add_offer_letter');
        abort_403(! in_array($addPermission, ['all', 'added']));

        $jobOffer = new RecruitJobOfferLetter;

        $jobOffer->recruit_job_application_id = $request->jobApplicant;
        $jobOffer->recruit_job_id = $request->jobId;
        $jobOffer->description = $request->description;


        $jobOffer->job_expire = Carbon::createFromFormat($this->company->date_format, $request->jobExpireDate)->format('Y-m-d');
        $jobOffer->expected_joining_date = Carbon::createFromFormat($this->company->date_format, $request->expJoinDate)->format('Y-m-d');

        $jobOffer->comp_amount = $request->comp_amount;
        $jobOffer->pay_according = $request->pay_according;
        $jobOffer->sign_require = $request->signature;
        $jobOffer->hash = md5(microtime());

        if ($request->save_type == 'send' || $request->save_type == 'save') {
            $jobOffer->status = 'pending';

        } else {
            $jobOffer->status = 'draft';
        }

        $jobOffer->add_structure = $request->add_structure ?? '0';
        $jobOffer->save();

        if (! empty($request->checkQuestionColumn)) {
            foreach ($request->checkQuestionColumn as $id) {
                $question = new RecruitJobOfferQuestion;
                $question->recruit_custom_question_id = $id;
                $question->recruit_job_offer_letter_id = $jobOffer->id;
                $question->save();
            }
        }

        // Send offer Letter mail if selected save and send

        $type = ($request->save_type == 'send') ? 'saveSend' : 'save';
        event(new OfferLetterEvent($jobOffer, $type));

        if ($request->add_structure == '1' && in_array('Payroll', worksuite_plugins())) {
            if ($request->fixedAllowance < 0) {
                return Reply::error('payroll::modules.payroll.fixedAllowanceError');
            }

            if ($request->annual_salary > 0) {
                $salary = new RecruitSalaryStructure;
                $salary->recruit_job_offer_letter_id = $jobOffer->id;
                $salary->recruit_job_application_id = $request->jobApplicant;
                $salary->annual_salary = $request->annual_salary;
                $salary->basic_salary = $request->basic_salary;
                $salary->basic_value_type = $request->basic_value;
                $salary->amount = $request->annual_salary / 12;
                $salary->fixed_allowance = $request->fixedAllowance;

                $earningsArray = [];

                if (! is_null($request->earning_variable)) {
                    foreach ($request->earning_variable as $key => $variableValue) {
                        $payrollComponents = SalaryComponent::where('id', $key)->get();

                        foreach ($payrollComponents as $key => $value) {
                            $earningsArray[$variableValue] = $value->component_name;
                            $arr = [];
                            array_push($arr, $earningsArray);
                        }
                    }
                }

                $deductionsArray = [];

                if (! is_null($request->deduction_variable)) {
                    foreach ($request->deduction_variable as $key => $variableValue) {
                        $payrollComponents = SalaryComponent::where('id', $key)->get();

                        foreach ($payrollComponents as $key => $value) {
                            $deductionsArray[$variableValue] = $value->component_name;
                            $arr = [];
                            array_push($arr, $deductionsArray);
                        }
                    }
                }

                $salaryComponents = [
                    'earnings' => $earningsArray,
                    'deductions' => $deductionsArray,
                ];
                $salaryComponentsJson = json_encode($salaryComponents);

                $salary->salary_json = $salaryComponentsJson;
                $salary->date = now()->timezone($this->company->timezone)->toDateString();
                $salary->save();

                if (! is_null($request->deduction_variable)) {
                    foreach ($request->deduction_variable as $key => $value) {
                        $payrollComponent = SalaryComponent::where('id', $key)->first();

                        $variable = new RecruitSelectedSalaryComponent;
                        $variable->rss_id = $salary->id;
                        $variable->component_name = $payrollComponent->component_name;
                        $variable->salary_component_id = $key;
                        $variable->component_type = $payrollComponent->component_type;
                        $variable->component_value = $value;
                        $variable->save();
                    }
                }

                if (! is_null($request->earning_variable)) {
                    foreach ($request->earning_variable as $key => $value) {

                        $payrollComponent = SalaryComponent::where('id', $key)->first();

                        $variable = new RecruitSelectedSalaryComponent;
                        $variable->rss_id = $salary->id;
                        $variable->component_name = $payrollComponent->component_name;
                        $variable->salary_component_id = $key;
                        $variable->component_type = $payrollComponent->component_type;
                        $variable->component_value = $value;
                        $variable->save();
                    }
                }
            }
        }

        $redirectUrl = urldecode($request->redirect_url);

        if ($redirectUrl == '') {
            $redirectUrl = route('job-offer-letter.index');
        }

        return Reply::successWithData(__('recruit::messages.offerAdded'), ['redirectUrl' => $redirectUrl, 'application_id' => $jobOffer->id]);
    }

    public function edit($id)
    {
        $this->jobOffer = RecruitJobOfferLetter::findOrFail($id);
        $this->letter = RecruitJobOfferLetter::with('job')->findOrFail($id);

        $this->editPermission = user()->permission('edit_offer_letter');
        abort_403(! ($this->editPermission == 'all'
            || ($this->editPermission == 'added' && $this->jobOffer->added_by == user()->id)
            || ($this->editPermission == 'owned' && user()->id == $this->letter->job->recruiter_id)
            || ($this->editPermission == 'both' && user()->id == $this->letter->job->recruiter_id)
            || $this->jobOffer->added_by == user()->id));

        $this->jobs = RecruitJob::all();
        $this->applications = RecruitJobApplication::where('recruit_job_id', $this->jobOffer->recruit_job_id)->get();
        $this->currency = $this->jobOffer ? Currency::where('id', '=', $this->letter->job->currency_id)->first() : null;
        $this->questions = RecruitCustomQuestion::where('company_id', '=', company()->id)->where('status', 'enable')->where('category', 'job_offer')->get();
        $this->allQuestions = RecruitJobOfferQuestion::where('recruit_job_offer_letter_id', $id)->get();
        $this->selectedQuestions = $this->allQuestions->pluck('recruit_custom_question_id')->toArray();
        $this->salaryStructure = null;
        $selectedEarningsComponents = null;
        $selectedDeductionComponents = null;
        $this->earningComponents = null;
        $this->deductionComponents = null;

        if (in_array('Payroll', worksuite_plugins())) {
            $this->salaryStructure = RecruitSalaryStructure::where('recruit_job_offer_letter_id', $id)->first() ?? null;

            if ($this->salaryStructure) {
                $selectedEarningsComponents = RecruitSelectedSalaryComponent::where('rss_id', $this->salaryStructure->id)->where('component_type', 'earning')->get() ?? null;
                $selectedDeductionComponents = RecruitSelectedSalaryComponent::where('rss_id', $this->salaryStructure->id)->where('component_type', 'deduction')->get() ?? null;
            }

            $earningsArray = [];

            if ($this->salaryStructure && $selectedEarningsComponents) {
                foreach ($selectedEarningsComponents as $selectedEarningsComponent) {
                    $payrollComponents = SalaryComponent::where('id', $selectedEarningsComponent->salary_component_id)->get();

                    foreach ($payrollComponents as $value) {
                        $earningsArray[$value->component_name] = $value->id;
                        $arr = [];
                        array_push($arr, $earningsArray);
                    }
                }
            }

            $this->earningsArray = $earningsArray;

            $deductionsArray = [];

            if ($this->salaryStructure && $selectedDeductionComponents) {
                foreach ($selectedDeductionComponents as $selectedDeductionComponent) {
                    $payrollComponents = SalaryComponent::where('id', $selectedDeductionComponent->salary_component_id)->get();

                    foreach ($payrollComponents as $value) {
                        $deductionsArray[$value->component_name] = $value->id;
                        $arr = [];
                        array_push($arr, $deductionsArray);
                    }
                }
            }

            $this->deductionsArray = $deductionsArray;

            $this->formSettingsEarn = collect([]);
            $formSettingsEarn = $earningsArray;

            if (! is_null($formSettingsEarn)) {
                foreach ($formSettingsEarn as $key => $value) {
                    $component = RecruitSelectedSalaryComponent::where('salary_component_id', $value)->first();
                    $this->formSettingsEarn->push($component);
                }
            } else {
                $this->formSettingsEarn = null;
            }

            $this->formSettingsDeduction = collect([]);
            $formSettingsDeduction = $deductionsArray;

            if (! is_null($formSettingsDeduction)) {
                foreach ($formSettingsDeduction as $key => $value) {
                    $component = RecruitSelectedSalaryComponent::where('salary_component_id', $value)->first();
                    $this->formSettingsDeduction->push($component);
                }
            } else {
                $this->formSettingsDeduction = null;
            }

            $this->payrollController = new EmployeeMonthlySalaryController;
            $this->earningComponents = SalaryComponent::where('component_type', 'earning')->get();
            $this->deductionComponents = SalaryComponent::where('component_type', 'deduction')->get();
        }

        if (request()->ajax()) {
            $html = view('recruit::jobs.ajax.editOfferLetter', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'recruit::jobs.ajax.editOfferLetter';

        return view('recruit::jobs.createOfferLetter', $this->data);
    }

    public function update(StoreOfferLetter $request, $id)
    {
        $jobOffer = RecruitJobOfferLetter::findOrFail($id);
        $this->letter = RecruitJobOfferLetter::with('job')->findOrFail($id);

        $this->editPermission = user()->permission('edit_offer_letter');
        abort_403(! ($this->editPermission == 'all'
            || ($this->editPermission == 'added' && $jobOffer->added_by == user()->id)
            || ($this->editPermission == 'owned' && user()->id == $this->letter->job->recruiter_id)
            || ($this->editPermission == 'both' && user()->id == $this->letter->job->recruiter_id)
            || $jobOffer->added_by == user()->id));
          info('Recruiter ID:', ['id' => $request->recruiter]);
        $jobOffer->recruit_job_application_id = $request->jobApplicant;
        $jobOffer->recruit_job_id = $request->jobId;
        $jobOffer->add_structure = $request->add_structure;
        $jobOffer->job_expire = Carbon::createFromFormat($this->company->date_format, $request->jobExpireDate)->format('Y-m-d');
        $jobOffer->expected_joining_date = Carbon::createFromFormat($this->company->date_format, $request->expJoinDate)->format('Y-m-d');

        $jobOffer->comp_amount = $request->comp_amount;
        $jobOffer->description = $request->description;
        $jobOffer->pay_according = $request->pay_according;

        if ($request->has('signature')) {
            $jobOffer->sign_require = $request->signature;
        }

        $jobOffer->status = $request->status;
        $jobOffer->save();

        if (! empty($request->checkQuestionColumn)) {
            RecruitJobOfferQuestion::where('recruit_job_offer_letter_id', $id)->delete();

            foreach ($request->checkQuestionColumn as $id) {
                $question = new RecruitJobOfferQuestion;
                $question->recruit_custom_question_id = $id;
                $question->recruit_job_offer_letter_id = $jobOffer->id;
                $question->save();
            }
        }

        if ($request->add_structure == '1' && in_array('Payroll', worksuite_plugins())) {
            $salaryStructure = RecruitSalaryStructure::where('recruit_job_offer_letter_id', $id)->first() ?? new RecruitSalaryStructure;

            if ($request->fixedAllowance < 0) {
                return Reply::error('payroll::modules.payroll.fixedAllowanceError');
            }

            if ($request->annual_salary > 0) {
                $salaryStructure->recruit_job_offer_letter_id = $jobOffer->id;
                $salaryStructure->recruit_job_application_id = $request->jobApplicant;
                $salaryStructure->annual_salary = $request->annual_salary;
                $salaryStructure->basic_salary = $request->basic_salary;
                $salaryStructure->basic_value_type = $request->basic_value;
                $salaryStructure->amount = $request->annual_salary / 12;
                $salaryStructure->fixed_allowance = $request->fixedAllowance;

                $earningsArray = [];

                if (! is_null($request->earning_variable)) {
                    foreach ($request->earning_variable as $key => $variableValue) {
                        $payrollComponents = SalaryComponent::where('id', $key)->get();

                        foreach ($payrollComponents as $key => $value) {
                            $earningsArray[$variableValue] = $value->component_name;
                            $arr = [];
                            array_push($arr, $earningsArray);
                        }
                    }
                }

                $deductionsArray = [];

                if (! is_null($request->deduction_variable)) {
                    foreach ($request->deduction_variable as $key => $variableValue) {
                        $payrollComponents = SalaryComponent::where('id', $key)->get();

                        foreach ($payrollComponents as $key => $value) {
                            $deductionsArray[$variableValue] = $value->component_name;
                            $arr = [];
                            array_push($arr, $deductionsArray);
                        }
                    }
                }

                $salaryComponents = [
                    'earnings' => $earningsArray,
                    'deductions' => $deductionsArray,
                ];
                $salaryComponentsJson = json_encode($salaryComponents);

                $salaryStructure->salary_json = $salaryComponentsJson;
                $salaryStructure->date = now()->timezone($this->company->timezone)->toDateString();
                $salaryStructure->save();
                RecruitSelectedSalaryComponent::where('rss_id', $salaryStructure->id)->delete();

                if (! is_null($request->deduction_variable)) {

                    foreach ($request->deduction_variable as $key => $value) {
                        $payrollComponent = SalaryComponent::where('id', $key)->first();

                        $variable = new RecruitSelectedSalaryComponent;
                        $variable->rss_id = $salaryStructure->id;
                        $variable->component_name = $payrollComponent->component_name;
                        $variable->salary_component_id = $key;
                        $variable->component_type = $payrollComponent->component_type;
                        $variable->component_value = $value;
                        $variable->save();
                    }
                }

                if (! is_null($request->earning_variable)) {

                    foreach ($request->earning_variable as $key => $value) {
                        $payrollComponent = SalaryComponent::where('id', $key)->first();

                        $variable = new RecruitSelectedSalaryComponent;
                        $variable->rss_id = $salaryStructure->id;
                        $variable->component_name = $payrollComponent->component_name;
                        $variable->salary_component_id = $key;
                        $variable->component_type = $payrollComponent->component_type;
                        $variable->component_value = $value;
                        $variable->save();
                    }
                }
            }
        }

        return Reply::successWithData(__('recruit::modules.message.updateSuccess'), ['redirectUrl' => route('job-offer-letter.index'), 'application_id' => $jobOffer->id]);
    }

    public function destroy($id)
    {
        $job = RecruitJobOfferLetter::findOrFail($id);
        $this->letter = RecruitJobOfferLetter::with('job')->findOrFail($id);

        $this->deletePermission = user()->permission('delete_offer_letter');
        abort_403(! ($this->deletePermission == 'all'
            || ($this->deletePermission == 'added' && $job->added_by == user()->id)
            || ($this->deletePermission == 'owned' && user()->id == $this->letter->job->recruiter_id)
            || ($this->deletePermission == 'both' && user()->id == $this->letter->job->recruiter_id)
            || $job->added_by == user()->id));

        $job->delete();

        return Reply::successWithData(__('recruit::modules.message.deleteSuccess'), ['redirectUrl' => route('job-offer-letter.index')]);
    }

    public function sendOffer(Request $request)
    {
        $jobOffer = RecruitJobOfferLetter::findOrFail($request->jobOfferId);
        event(new OfferLetterEvent($jobOffer, 'send'));
        $jobOffer->status = 'pending';
        $jobOffer->save();

        return Reply::successWithData(__('recruit::modules.message.mailsent'), ['redirectUrl' => route('job-offer-letter.index')]);
    }

    public function withdrawOffer(Request $request)
    {
        $jobOffer = RecruitJobOfferLetter::findOrFail($request->id);
        $jobOffer->status = 'withdraw';
        $jobOffer->save();

        return Reply::successWithData(__('recruit::modules.message.withdraw'), ['redirectUrl' => route('job-offer-letter.index')]);
    }

    public function fetchApplication(Request $request)
    {
        $data = RecruitJobApplication::where('recruit_job_id', $request->job_id)->get();
        $jobData = RecruitJob::where('id', $request->job_id)->first();

        $jobId = $request->job_id;
        $dataStage = RecruitJob::with('stages')->findOrFail($jobId);
        $dataStage = $dataStage->stages->pluck('name', 'id')->toArray();
        $currencySymbol = Currency::where('id', '=', $jobData->currency_id)->first();
        $payAccording = __('recruit::app.job.' . $jobData->pay_according);

        return Reply::dataOnly(['status' => 'success', 'stages' => $dataStage, 'applications' => $data, 'job' => $jobData, 'id' => $request->job_id, 'currencySymbol' => $currencySymbol, 'payAccording' => $payAccording]);
    }

    public function applyQuickAction(Request $request)
    {
        switch ($request->action_type) {
        case 'delete':
            $this->deleteRecords($request);

            return Reply::success(__('messages.deleteSuccess'));
        case 'change-status':
            $this->changeStatus($request);

            return Reply::success(__('messages.updateSuccess'));
        default:
            return Reply::error(__('messages.selectAction'));
        }
    }

    protected function deleteRecords($request)
    {
        abort_403(user()->permission('delete_offer_letter') != 'all');
        RecruitJobOfferLetter::whereIn('id', explode(',', $request->row_ids))->delete();

        return true;
    }

    protected function changeStatus($request)
    {
        abort_403(user()->permission('edit_offer_letter') != 'all');
        RecruitJobOfferLetter::whereIn('id', explode(',', $request->row_ids))->update(['status' => $request->status]);

        return true;
    }

    public function changeLetterStatus(Request $request)
    {
        abort_403(user()->permission('edit_offer_letter') != 'all');
        $letterId = $request->letterId;
        $status = $request->status;
        $letterStatus = RecruitJobOfferLetter::findOrFail($letterId);
        $letterStatus->status = $status;
        $letterStatus->save();

        return Reply::success(__('messages.updateSuccess'));
    }

    public function createEmployee($id)
    {
        $addPermission = user()->permission('add_employees');
        abort_403(! in_array($addPermission, ['all', 'added']));
        $this->lastEmployeeID = EmployeeDetails::max('id');
        $this->designations = Designation::allDesignations();
        $this->teams = Team::all();
        $this->employees = User::allEmployees(null, true);
        $this->offerLetter = RecruitJobOfferLetter::with('jobApplication', 'jobApplication.job')->findOrFail($id);
        $this->countries = countries();

        return view('recruit::jobs.offer-letter.create_employee', $this->data);
    }

    public function createDesignation()
    {
        $this->designations = Designation::all();

        return view('recruit::jobs.offer-letter.create_designation', $this->data);
    }

    public function designationStore(DesignationStore $request)
    {
        $group = new Designation();
        $group->name = $request->name;
        $group->parent_id = $request->parent_id ? $request->parent_id : null;
        $group->save();

        $redirectUrl = urldecode($request->redirect_url);

        if ($redirectUrl == '') {
            $redirectUrl = route('designations.index');
        }

        $newDesignationId = $group->id;


        $allDesignation = Designation::all();


        $options = BaseModel::options($allDesignation);
        $selectedDesignationId = $group->id;

        return Reply::successWithData(__('messages.recordSaved'), ['data' => $options, 'selectedId' => $selectedDesignationId,'selected' => $newDesignationId,]);
    }

    public function employeeStore(EmployeeStoreRequest $request)
    {
        $addPermission = user()->permission('add_employees');
        abort_403(! in_array($addPermission, ['all', 'added']));

        DB::beginTransaction();
        try {

            $userAuth = UserAuth::createUserAuthCredentials($request->email);

            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->country_id = $request->country;
            $user->user_auth_id = $userAuth->id;
            $user->save();

            if ($user->id) {
                $employee = new EmployeeDetails;
                $employee->user_id = $user->id;
                $employee->employee_id = $request->employee_id;
                $employee->joining_date = Carbon::createFromFormat($this->company->date_format, $request->joining_date)->format('Y-m-d');
                $employee->department_id = $request->department;
                $employee->designation_id = $request->designation;
                $employee->reporting_to = $request->reporting_to;
                $employee->save();
            }

            $employeeRole = Role::where('name', 'employee')->first();
            $user->attachRole($employeeRole);
            $user->assignUserRolePermission($employeeRole->id);
            $this->logSearchEntry($user->id, $user->name, 'employees.show', 'employee');

            if ($request->has('offer_letter_id') && ! is_null($request->offer_letter_id)) {
                $offerLetter = RecruitJobOfferLetter::findOrFail($request->offer_letter_id);
                $offerLetter->employee_id = $user->id;
                $offerLetter->save();
            }

            // Commit Transaction
            DB::commit();

        } catch (TransportException $e) {
            // Rollback Transaction
            DB::rollback();

            return Reply::error('Please configure SMTP details to add employee. Visit Settings -> notification setting to set smtp', 'smtp_error');
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();

            return Reply::error('Some error occurred when inserting the data. Please try again or contact support: ' . $e->getMessage());
        }

        return Reply::successWithData(__('messages.recordSaved'), []);
    }

    public function fetchComponent()
    {
        $currency_id = request()->currencyId ?? company()->currency->id;

        if (! is_null($currency_id)) {
            $this->currency = Currency::where('id', $currency_id)->first();
        }

        $this->formSettings = collect([]);

        $formSettings = request()->component_id;
        $deductionCount = 0;

        if (! is_null($formSettings)) {
            foreach ($formSettings as $form) {
                $component = SalaryComponent::where('id', $form)->first();

                if ($component->component_type == 'deduction') {
                    $deductionCount = $deductionCount + 1;
                }

                $this->formSettings->push($component);
            }
        } else {
            $this->formSettings = null;
        }

        $this->deductionCount = $deductionCount;

        if (in_array('Payroll', worksuite_plugins())) {
            $this->payrollController = new EmployeeMonthlySalaryController;
            $this->earningComponents = SalaryComponent::where('component_type', 'earning')->get();
            $this->deductionComponents = SalaryComponent::where('component_type', 'deduction')->get();
            $this->salaryGroup = $this->formSettings;
        }

        $html = view('recruit::jobs.salary-components.fetch-component', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
    }

    public function getSalary(Request $request)
    {
        $this->currency = Currency::where('id', $request->currency_id)->first();

        if ($request->basicType == 'fixed') {
            $this->basicSalary = $request->basicValue;
        }
        else {
            $this->basicSalary = ($request->annualSalary / 12) / 100 * $request->basicValue;
        }

        $this->annualSalary = $request->annualSalary;

        $this->salaryGroup = collect([]);
        $formSettings = $request->componentIds ?? null;

        if (! is_null($formSettings)) {
            foreach ($formSettings as $form) {
                $component = SalaryComponent::where('id', $form)->first();
                $this->salaryGroup->push($component);
            }
        } else {
            $this->salaryGroup = null;
        }

        $this->basicType = $request->basicType;
        $totalEarnings = [];
        $totalDeductions = [];

        $this->payrollController = new EmployeeMonthlySalaryController;
        $deductionCount = 0;

        if (! is_null($this->salaryGroup)) {
            foreach ($this->salaryGroup as $component) {

                if ($component->component_type == 'earning') {
                    $totalEarnings[] = $component->component_value;
                }
                else {
                    $deductionCount = $deductionCount + 1;
                    $totalDeductions[] = $component->component_value;
                }
            }
        }

        $this->deductionCount = $deductionCount;
        $this->totalEarnings = $totalEarnings;
        $this->totalDeductions = $totalDeductions;

        $this->fixedAllowance = ($request->annualSalary / 12 - $this->basicSalary) - (array_sum($totalDeductions) + array_sum($totalEarnings));

        $html = view('recruit::jobs.salary-components.salary-calculation', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'component' => $html, 'id' => $request->userId]);
    }

    public function fetchedCurrency()
    {
        $this->currency = collect([]);
        $currency_id = request()->currencyId['id'];

        $this->currency = Currency::where('id', $currency_id)->first();

        if (in_array('Payroll', worksuite_plugins())) {
            $this->allComponents = SalaryComponent::all();
            $this->payrollController = new EmployeeMonthlySalaryController;
            $this->earningComponents = SalaryComponent::where('component_type', 'earning')->get();
            $this->deductionComponents = SalaryComponent::where('component_type', 'deduction')->get();
        }
        else {
            $this->allComponents = null;
            $this->payrollController = null;
            $this->earningComponents = null;
            $this->deductionComponents = null;
        }

        $html = view('recruit::jobs.salary-components.default-structure', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'html' => $html]);
    }

}
