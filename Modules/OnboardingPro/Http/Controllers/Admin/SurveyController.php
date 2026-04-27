<?php
namespace Modules\OnboardingPro\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Modules\OnboardingPro\Entities\Survey;

class SurveyController extends AccountBaseController
{
    public function index()
    {
        $this->pageTitle = __('onboardingpro::onboardingpro.surveys');
        $surveys = Survey::orderBy('created_at', 'desc')->get();
        return view('onboardingpro::admin.surveys.index', compact('surveys'));
    }

    public function create()
    {
        $this->pageTitle = __('onboardingpro::onboardingpro.add_survey');
        return view('onboardingpro::admin.surveys.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions'   => 'required|array|min:1',
            'role'        => 'required|in:admin,employee,client,all',
            'trigger'     => 'required|in:first_login,post_onboarding,milestone',
            'active'      => 'boolean',
        ]);

        Survey::create($data);

        return Reply::success(__('onboardingpro::onboardingpro.survey_created'));
    }

    public function edit(Survey $survey)
    {
        $this->pageTitle = __('onboardingpro::onboardingpro.edit_survey');
        return view('onboardingpro::admin.surveys.edit', compact('survey'));
    }

    public function update(Request $request, Survey $survey)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions'   => 'required|array|min:1',
            'role'        => 'required|in:admin,employee,client,all',
            'trigger'     => 'required|in:first_login,post_onboarding,milestone',
            'active'      => 'boolean',
        ]);

        $survey->update($data);

        return Reply::success(__('onboardingpro::onboardingpro.survey_updated'));
    }

    public function destroy(Survey $survey)
    {
        $survey->delete();
        return Reply::success(__('onboardingpro::onboardingpro.survey_deleted'));
    }

    public function show(Survey $survey)
    {
        return view('onboardingpro::admin.surveys.edit', compact('survey'));
    }
}
