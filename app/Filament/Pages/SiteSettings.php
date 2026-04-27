<?php

namespace App\Filament\Pages;

use App\Models\PlatformSetting;
use Filament\Forms;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;

class SiteSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paint-brush';

    protected static string|\UnitEnum|null $navigationGroup = 'Platform';

    protected static ?int $navigationSort = 900;
    protected static ?string $navigationLabel = 'Site Settings';
    protected string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = PlatformSetting::current();
        $this->form->fill([
            'app_name' => $settings->app_name,
            'site_name' => $settings->site_name ?: $settings->app_name,
            'logo_path' => $settings->logo_path ?: $settings->logo,
            'favicon_path' => $settings->favicon_path ?: $settings->favicon,
            'primary_color' => $settings->primary_color,
            'secondary_color' => $settings->secondary_color,
            'accent_color' => $settings->accent_color,
            'support_email' => $settings->support_email,
            'billing_email' => $settings->billing_email,
            'contact_phone' => $settings->contact_phone,
            'footer_text' => $settings->footer_text,
            'meta_title' => $settings->meta_title,
            'meta_description' => $settings->meta_description,
            'landing_headline' => $settings->landing_headline,
            'landing_subheadline' => $settings->landing_subheadline,
            'cta_label' => $settings->cta_label,
            'cta_url' => $settings->cta_url,
            'enable_registration' => $settings->enable_registration,
            'maintenance_message' => $settings->maintenance_message,
            'custom_css' => $settings->custom_css,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Branding')->description('Controls the SaaS name, logo, favicon, theme colors, and shared UI branding.')->schema([
                Forms\Components\TextInput::make('app_name')->label('Application name')->required()->maxLength(80),
                Forms\Components\TextInput::make('site_name')->label('Public site name')->maxLength(80),
                Forms\Components\FileUpload::make('logo_path')->label('Logo')->disk('public')->directory('platform')->image()->imageEditor()->preserveFilenames()->downloadable()->openable(),
                Forms\Components\FileUpload::make('favicon_path')->label('Favicon')->disk('public')->directory('platform')->image()->preserveFilenames()->downloadable()->openable(),
                Forms\Components\ColorPicker::make('primary_color')->label('Primary color'),
                Forms\Components\ColorPicker::make('secondary_color')->label('Secondary color'),
                Forms\Components\ColorPicker::make('accent_color')->label('Accent color'),
            ])->columns(2),
            Section::make('Contact & support')->description('Shown in public pages, billing flows, emails, and footer areas.')->schema([
                Forms\Components\TextInput::make('support_email')->label('Support email')->email()->maxLength(120),
                Forms\Components\TextInput::make('billing_email')->label('Billing email')->email()->maxLength(120),
                Forms\Components\TextInput::make('contact_phone')->label('Contact phone')->tel()->maxLength(60),
                Forms\Components\Textarea::make('footer_text')->label('Footer text')->rows(3)->maxLength(500),
            ])->columns(2),
            Section::make('Public site')->description('Homepage copy, SEO metadata, registration gate, and optional announcement banner.')->schema([
                Forms\Components\TextInput::make('meta_title')->label('Meta title')->maxLength(120),
                Forms\Components\Textarea::make('meta_description')->label('Meta description')->rows(2)->maxLength(300),
                Forms\Components\TextInput::make('landing_headline')->label('Landing headline')->maxLength(160),
                Forms\Components\Textarea::make('landing_subheadline')->label('Landing subheadline')->rows(2)->maxLength(300),
                Forms\Components\TextInput::make('cta_label')->label('CTA label')->maxLength(80),
                Forms\Components\TextInput::make('cta_url')->label('CTA URL')->maxLength(255),
                Forms\Components\Toggle::make('enable_registration')->label('Enable public registration')->default(true),
                Forms\Components\Textarea::make('maintenance_message')->label('Maintenance / announcement banner')->rows(2)->maxLength(300),
            ])->columns(2),
            Section::make('Advanced')->description('Optional CSS injected into the main Inertia app shell. Keep this for small brand tweaks only.')->schema([
                Forms\Components\Textarea::make('custom_css')->label('Custom CSS')->rows(8)->columnSpanFull(),
            ]),
        ])->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $state['logo'] = $state['logo_path'] ?? null;
        $state['favicon'] = $state['favicon_path'] ?? null;
        $state['site_name'] = $state['site_name'] ?: ($state['app_name'] ?? 'TITAN ZERO');

        PlatformSetting::current()->update($state);
        cache()->forget('platform_settings');

        Notification::make()->title('Site settings saved')->success()->send();
    }
}
