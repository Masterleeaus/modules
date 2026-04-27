<?php

namespace App\Http\Middleware;

use App\Models\PlatformSetting;
use App\Services\PlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();
        $subscription = null;
        $planData = null;

        if ($user && $user->organization_id) {
            $org         = $user->organization;
            $orgId       = $org->id;
            $planService = app(PlanService::class);

            $activeSub = Cache::remember(
                "org.{$orgId}.active_subscription",
                300,
                fn () => $org->activeSubscription()
            );

            $activePlan = Cache::remember(
                "org.{$orgId}.active_plan",
                300,
                fn () => $planService->activePlan($org)
            );

            $techCount = Cache::remember(
                "org.{$orgId}.tech_count",
                300,
                fn () => $planService->technicianCount($org)
            );

            $techLimit    = PlanService::TECHNICIAN_LIMITS[$activePlan] ?? null;
            $atTechLimit  = $techLimit !== null && $techCount >= $techLimit;

            if ($activeSub) {
                $subscription = [
                    'status'         => $activeSub->status,
                    'plan'           => $org->plan,
                    'active_plan'    => $activePlan,
                    'is_trialing'    => $activeSub->isTrialing(),
                    'days_remaining' => $activeSub->trialDaysRemaining(),
                    'trial_ends_at'  => $activeSub->trial_ends_at?->toIso8601String(),
                ];
            }

            $planData = [
                'current'       => $org->plan,
                'active'        => $activePlan,
                'tech_limit'    => $techLimit,
                'tech_count'    => $techCount,
                'at_tech_limit' => $atTechLimit,
            ];
        }

        $platformSettings = Cache::remember("platform_settings", 300, function () {
            $settings = PlatformSetting::current();

            return [
                "app_name" => $settings->brandName(),
                "site_name" => $settings->site_name ?: $settings->brandName(),
                "logo" => $settings->logo_path ?: $settings->logo,
                "logo_path" => $settings->logo_path ?: $settings->logo,
                "logo_url" => $settings->logoUrl(),
                "favicon" => $settings->favicon_path ?: $settings->favicon,
                "favicon_path" => $settings->favicon_path ?: $settings->favicon,
                "favicon_url" => $settings->faviconUrl(),
                "primary_color" => $settings->primary_color,
                "secondary_color" => $settings->secondary_color,
                "accent_color" => $settings->accent_color,
                "support_email" => $settings->support_email,
                "billing_email" => $settings->billing_email,
                "contact_phone" => $settings->contact_phone,
                "footer_text" => $settings->footer_text,
                "meta_title" => $settings->meta_title,
                "meta_description" => $settings->meta_description,
                "landing_headline" => $settings->landing_headline,
                "landing_subheadline" => $settings->landing_subheadline,
                "cta_label" => $settings->cta_label,
                "cta_url" => $settings->cta_url,
                "enable_registration" => (bool) $settings->enable_registration,
                "maintenance_message" => $settings->maintenance_message,
            ];
        });


        return [
            ...parent::share($request),
            'auth' => [
                'user'  => $user,
                'roles' => $user?->getRoleNames() ?? [],
            ],
            'subscription' => $subscription,
            'plan'         => $planData,
            'platform'     => $platformSettings,
        ];
    }
}
