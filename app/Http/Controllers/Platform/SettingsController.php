<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function edit(): Response
    {
        $settings = $this->settings();

        return Inertia::render('Platform/Settings', [
            'settings' => $this->payload($settings),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = $this->settings();

        $data = $request->validate([
            'app_name' => ['required', 'string', 'max:255'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'support_email' => ['nullable', 'email', 'max:255'],
            'footer_text' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:1024'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_favicon' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('remove_logo') && $settings->logo_path) {
            Storage::disk('public')->delete($settings->logo_path);
            $settings->logo_path = null;
        }

        if ($request->boolean('remove_favicon') && $settings->favicon_path) {
            Storage::disk('public')->delete($settings->favicon_path);
            $settings->favicon_path = null;
        }

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $settings->logo_path = $request->file('logo')->store('platform', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($settings->favicon_path) {
                Storage::disk('public')->delete($settings->favicon_path);
            }
            $settings->favicon_path = $request->file('favicon')->store('platform', 'public');
        }

        $settings->fill([
            'app_name' => $data['app_name'],
            'primary_color' => $data['primary_color'] ?: '#2563eb',
            'support_email' => $data['support_email'] ?? null,
            'footer_text' => $data['footer_text'] ?? null,
        ])->save();

        Cache::forget('platform.settings');

        return back()->with('success', 'Platform settings updated.');
    }

    private function settings(): PlatformSetting
    {
        return PlatformSetting::firstOrCreate([], [
            'app_name' => config('app.name', 'FieldOps Hub'),
            'primary_color' => '#2563eb',
        ]);
    }

    private function payload(PlatformSetting $settings): array
    {
        return [
            'app_name' => $settings->app_name,
            'primary_color' => $settings->primary_color,
            'support_email' => $settings->support_email,
            'footer_text' => $settings->footer_text,
            'logo_url' => $settings->logo_url,
            'favicon_url' => $settings->favicon_url,
        ];
    }
}
