<?php
namespace Modules\OnboardingPro\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Modules\OnboardingPro\Entities\Banner;
use Modules\OnboardingPro\Entities\BannerUser;
use Modules\OnboardingPro\Entities\Survey;
use Modules\OnboardingPro\Entities\SurveyUser;

class OnboardingProController extends AccountBaseController
{
    /**
     * Returns current onboarding state for the authenticated user.
     */
    public function status(Request $request)
    {
        $user = auth()->user();
        $role = $this->resolveRole($user);

        // Banners pending (active, user role matches, not dismissed)
        $pendingBanners = Banner::active()
            ->forRole($role)
            ->orderBy('order')
            ->get()
            ->filter(fn($banner) => ! $banner->isDismissedBy($user))
            ->values();

        // Surveys pending
        $pendingSurveys = Survey::active()
            ->forRole($role)
            ->get()
            ->filter(fn($survey) => ! $survey->isCompletedBy($user))
            ->values();

        $totalSteps       = $pendingBanners->count() + $pendingSurveys->count();
        $completedBanners = Banner::active()->forRole($role)->get()
            ->filter(fn($b) => $b->isDismissedBy($user))->count();
        $completedSurveys = Survey::active()->forRole($role)->get()
            ->filter(fn($s) => $s->isCompletedBy($user))->count();
        $completedSteps   = $completedBanners + $completedSurveys;
        $totalAll         = $completedSteps + $totalSteps;

        $percent = $totalAll > 0 ? (int) round(($completedSteps / $totalAll) * 100) : 100;

        return Reply::dataOnly([
            'role'            => $role,
            'pending_banners' => $pendingBanners,
            'pending_surveys' => $pendingSurveys,
            'completed_pct'   => $percent,
            'is_complete'     => $totalSteps === 0,
        ]);
    }

    /**
     * Advance user to the next onboarding step.
     */
    public function nextStep(Request $request)
    {
        $user = auth()->user();
        $role = $this->resolveRole($user);
        $flow = config('onboarding-pro.flows.' . $role, config('onboarding-pro.flows.default'));

        foreach ($flow as $step) {
            if ($step['type'] === 'survey') {
                $survey = Survey::active()
                    ->where('trigger', $step['trigger'] ?? 'first_login')
                    ->forRole($role)
                    ->first();
                if ($survey && ! $survey->isCompletedBy($user)) {
                    return Reply::dataOnly(['next' => 'survey', 'survey' => $survey]);
                }
            }

            if ($step['type'] === 'banners') {
                $banner = Banner::active()
                    ->forRole($step['role'] ?? $role)
                    ->orderBy('order')
                    ->get()
                    ->first(fn($b) => ! $b->isDismissedBy($user));
                if ($banner) {
                    return Reply::dataOnly(['next' => 'banner', 'banner' => $banner]);
                }
            }
        }

        return Reply::dataOnly(['next' => 'complete']);
    }

    /**
     * Mark the onboarding flow as complete for the authenticated user.
     */
    public function complete(Request $request)
    {
        $user = auth()->user();
        $role = $this->resolveRole($user);
        $now  = now();

        // Dismiss all remaining banners
        Banner::active()->forRole($role)->orderBy('order')->get()
            ->each(function (Banner $banner) use ($user, $now) {
                BannerUser::firstOrCreate(
                    ['user_id' => $user->id, 'banner_id' => $banner->id],
                    ['seen_at' => $now, 'dismissed_at' => $now]
                )->update(['dismissed_at' => $now]);
            });

        return Reply::success(__('onboardingpro::onboardingpro.onboarding_complete'));
    }

    /**
     * Process and store survey responses.
     */
    public function submit(Request $request)
    {
        $request->validate([
            'survey_id' => 'required|integer|exists:surveys,id',
            'responses' => 'required|array',
        ]);

        $user   = auth()->user();
        $survey = Survey::findOrFail($request->survey_id);

        SurveyUser::updateOrCreate(
            ['user_id' => $user->id, 'survey_id' => $survey->id],
            [
                'responses'    => $request->responses,
                'completed_at' => now(),
                'step'         => count($request->responses),
            ]
        );

        return Reply::success(__('onboardingpro::onboardingpro.survey_submitted'));
    }

    /**
     * Dismiss a specific banner for the authenticated user.
     */
    public function dismiss(Banner $banner)
    {
        $user = auth()->user();
        $now  = now();

        BannerUser::updateOrCreate(
            ['user_id' => $user->id, 'banner_id' => $banner->id],
            ['seen_at' => $now, 'dismissed_at' => $now]
        );

        return Reply::success(__('onboardingpro::onboardingpro.banner_dismissed'));
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function resolveRole($user): string
    {
        if (method_exists($user, 'hasRole')) {
            foreach (['admin', 'client', 'employee'] as $role) {
                if ($user->hasRole($role)) {
                    return $role;
                }
            }
        }
        return 'employee';
    }
}
