<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * TITAN STUDIO — Marketing Automation Full-Screen SPA
 *
 * Audience: Marketing agents / content creators
 * Type: Full-Screen SPA (React SPA with template engine + media editor, desktop optimized)
 *
 * Features:
 * - Social media content creation (AI-assisted)
 * - Landing page builder (drag-drop, AI-powered)
 * - Email campaign templates
 * - Newsletter scheduling
 * - Review management
 * - Referral program setup
 * - Analytics dashboard
 * - AI-assisted workflow (social posts, landing pages, campaigns, newsletters, reviews)
 */
class TitanStudioPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('titan-studio')
            ->path('titan-studio')
            ->brandName('TITAN STUDIO — Marketing')
            ->colors([
                'primary' => Color::Pink,
            ])
            ->login()
            ->discoverResources(
                in: app_path('Filament/TitanStudio/Resources'),
                for: 'App\\Filament\\TitanStudio\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/TitanStudio/Pages'),
                for: 'App\\Filament\\TitanStudio\\Pages'
            )
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/TitanStudio/Widgets'),
                for: 'App\\Filament\\TitanStudio\\Widgets'
            )
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
