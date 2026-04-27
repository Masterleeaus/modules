<?php
namespace Modules\OnboardingPro\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Modules\OnboardingPro\Entities\IntroductionStyle;

class IntroductionStyleController extends AccountBaseController
{
    public function index()
    {
        $this->pageTitle = __('onboardingpro::onboardingpro.introduction_styles');
        $styles = IntroductionStyle::orderBy('id')->get();
        $active = IntroductionStyle::where('active', true)->first();
        return view('onboardingpro::admin.styles.index', compact('styles', 'active'));
    }

    public function activate(Request $request, IntroductionStyle $style)
    {
        IntroductionStyle::query()->update(['active' => false]);
        $style->update(['active' => true]);
        return Reply::success(__('onboardingpro::onboardingpro.style_activated'));
    }
}
