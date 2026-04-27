<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PlatformSetting extends Model
{
    protected $fillable = [
        'app_name','site_name','logo','logo_path','favicon','favicon_path',
        'primary_color','secondary_color','accent_color',
        'support_email','billing_email','contact_phone','footer_text',
        'meta_title','meta_description','landing_headline','landing_subheadline',
        'cta_label','cta_url','enable_registration','maintenance_message','custom_css',
    ];

    protected $casts = ['enable_registration' => 'boolean'];

    public static function current(): self
    {
        $settings = static::query()->firstOrCreate([], [
            'app_name' => 'TITAN ZERO',
            'site_name' => 'TITAN ZERO',
            'logo_path' => 'platform/titan-zero-logo.png',
            'logo' => 'platform/titan-zero-logo.png',
            'primary_color' => '#2563eb',
            'secondary_color' => '#0f172a',
            'accent_color' => '#14b8a6',
            'support_email' => 'support@titanzero.pro',
            'footer_text' => 'Powered by Titan Zero.',
            'meta_title' => 'TITAN ZERO',
            'meta_description' => 'Titan Zero field operations platform.',
            'landing_headline' => 'Field operations, controlled from one hub.',
            'landing_subheadline' => 'Manage jobs, teams, invoices, dispatch, and SaaS tenants from Titan Zero.',
            'cta_label' => 'Get started',
            'cta_url' => '/register',
            'enable_registration' => true,
        ]);

        $dirty = false;
        foreach ([['site_name','app_name'], ['app_name','site_name'], ['logo_path','logo'], ['logo','logo_path'], ['favicon_path','favicon'], ['favicon','favicon_path']] as [$target, $source]) {
            if (! $settings->{$target} && $settings->{$source}) {
                $settings->{$target} = $settings->{$source};
                $dirty = true;
            }
        }
        if ($dirty) {
            $settings->save();
        }

        return $settings;
    }

    public function brandName(): string
    {
        return $this->app_name ?: $this->site_name ?: 'TITAN ZERO';
    }

    public function logoUrl(): ?string
    {
        return $this->publicUrl($this->logo_path ?: $this->logo) ?: asset('titan-zero-logo.png');
    }

    public function faviconUrl(): ?string
    {
        return $this->publicUrl($this->favicon_path ?: $this->favicon);
    }

    protected function publicUrl(?string $path): ?string
    {
        if (! $path) return null;
        return str_starts_with($path, 'http') ? $path : Storage::disk('public')->url($path);
    }
}
