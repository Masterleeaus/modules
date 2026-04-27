<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Files;
use App\Helper\Reply;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Modules\Recruit\Entities\RecruitSetting;
use App\Http\Controllers\AccountBaseController;
use App\Models\Company;
use App\Models\User;
use Modules\Recruit\Entities\ApplicationSource;
use Modules\Recruit\Entities\RecruitCustomQuestion;
use Modules\Recruit\Entities\RecruitEmailNotificationSetting;
use Modules\Recruit\Entities\Recruiter;
use Modules\Recruit\Entities\RecruitFooterLink;
use Modules\Recruit\Entities\RecruitJobCustomQuestion;
use Modules\Recruit\Entities\RecruitApplicationStatus;
use Modules\Recruit\Http\Requests\RecruitSetting\StoreSettingRequest;

class RecruitSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'recruit::app.menu.recruitSetting';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array(RecruitSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });

    }

    public function index()
    {
        $this->mail = RecruitSetting::where('company_id', company()->id)->first();
        $this->recruiters = Recruiter::with('user')->get();
        $this->employees = User::allEmployees()->all();
        $this->selectedRecruiter = Recruiter::get()->pluck('user_id')->toArray();
        $this->activeSettingMenu = 'recruit_settings';
        $this->emailSettings = RecruitEmailNotificationSetting::all();
        $this->footerLinks = RecruitFooterLink::where('company_id', company()->id)->get();
        $this->jobQuestions = RecruitCustomQuestion::where('company_id', company()->id)->get();
        $this->statuses = RecruitApplicationStatus::with('category')->where('company_id', company()->id)->get();
        $this->sources = ApplicationSource::where('company_id', company()->id)->get();


        $tab = request('tab');

        switch ($tab) {
        case 'recruit-setting':
            $this->view = 'recruit::recruit-setting.ajax.recruit-setting';
            break;
        case 'footer-settings':
            $this->view = 'recruit::recruit-setting.ajax.footer-settings';
            break;
        case 'recruit-email-notification-setting':
            $this->view = 'recruit::recruit-setting.ajax.recruit-email-notification-setting';
            break;
        case 'job-application-status-settings':
            $this->view = 'recruit::recruit-setting.ajax.job-application-status-settings';
            break;
        case 'recruit-custom-question-setting':
            $this->view = 'recruit::recruit-setting.ajax.custom-question-settings';
            break;
        case 'recruit-source-setting':
                $this->view = 'recruit::recruit-setting.ajax.source-setting';
                break;
        default:
            $this->general = RecruitSetting::where('company_id', '=', company()->id)->select('about')->first();
            $this->view = 'recruit::recruit-setting.ajax.general-setting';
            break;
        }

        $this->activeTab = $tab ?: 'general-setting';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle, 'activeTab' => $this->activeTab]);
        }

        return view('recruit::recruit-setting.index', $this->data);
    }

    public function update(StoreSettingRequest $request)
    {
        $settings = RecruitSetting::where('company_id', company()->id)->first();

        $formSetting = [];
        $ar = $request->checkColumns;

        foreach ($settings->form_settings as $id => $from) {
            $from['status'] = false;

            if ($request->has('checkColumns') && in_array($id, $ar)) {
                $from['status'] = true;
            }

            $formSetting = Arr::add($formSetting, $id, $from);
        }

        // Background image

        if ($request->image_delete == 'yes') {
            Files::deleteFile($settings->background_image, 'background');
            $settings->background_image = null;
        }
        elseif ($request->type == 'bg-image') {
            $oldImage = $settings->background_image;

            if ($request->hasFile('image')) {
                $settings->background_image = Files::uploadLocalOrS3($request->image, 'background');

                $path = Files::UPLOAD_FOLDER . '/background' . '/' . $oldImage;

                if (\File::exists($path)) {
                    Files::deleteFile($oldImage, 'background');
                }
            }
        }
        elseif ($request->type == 'bg-color') {
            $settings->background_color = $request->logo_background_color;
        }

        // front page logo

        if ($request->logo_delete == 'yes') {
            Files::deleteFile($settings->logo, 'company-logo');
            $settings->logo = null;
        }

        if ($request->hasFile('logo')) {
            Files::deleteFile($settings->logo, 'company-logo');
            $settings->logo = Files::uploadLocalOrS3($request->logo, 'company-logo');
        }

        if ($request->favicon_delete == 'yes') {
            Files::deleteFile($settings->favicon, 'company-favicon');
            $settings->favicon = null;
        }

        if ($request->hasFile('favicon')) {
            $settings->favicon = Files::uploadLocalOrS3($request->favicon, 'company-favicon', null, null, false);
        }

        $settings->career_site = $request->career_site;
        $settings->job_alert_status = $request->job_alert_status ?? 'no';
        $settings->google_recaptcha_status = $request->google_recaptcha_status ?? 'deactive';
        session()->forget('messageforAdmin');
        $settings->company_name = $request->company_name;
        $settings->application_restriction = $request->application_restriction;
        $settings->offer_letter_reminder = $request->offer_letter_reminder;
        $settings->company_website = $request->company_website;
        $settings->about = $request->about;
        $settings->type = $request->type;
        $settings->form_settings = $formSetting;
        $settings->legal_term = ($request->description == '<p><br></p>') ? null : $request->description;
        $settings->save();

        return Reply::successWithData(__('recruit::messages.settingupdated'), ['redirectUrl' => route('recruit-settings.index')]);
    }

}
