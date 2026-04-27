<?php

namespace App\Http\Middleware;

use App\Models\PlatformSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class HandleAppearance
{
    public function handle(Request $request, Closure $next): Response
    {
        $appearance = $request->cookie('appearance');
        $settings = Cache::remember('platform_settings', 300, fn () => PlatformSetting::current());

        View::share('appearance', in_array($appearance, ['light', 'dark', 'system']) ? $appearance : 'system');
        View::share('platformBranding', [
            'app_name' => $settings->brandName(),
            'site_name' => $settings->site_name ?: $settings->brandName(),
            'logo_url' => $settings->logoUrl(),
            'favicon_url' => $settings->faviconUrl(),
            'primary_color' => $settings->primary_color ?: '#2563eb',
            'secondary_color' => $settings->secondary_color ?: '#0f172a',
            'accent_color' => $settings->accent_color ?: '#14b8a6',
            'support_email' => $settings->support_email,
            'footer_text' => $settings->footer_text,
            'meta_title' => $settings->meta_title ?: $settings->brandName(),
            'meta_description' => $settings->meta_description,
            'custom_css' => $settings->custom_css,
        ]);

        return $next($request);
    }
}
