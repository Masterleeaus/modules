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
 * TITAN HELLO — Lead Gen Management SPA Control Panel
 *
 * Audience: Lead gen specialists / marketing agents
 * Type: Full-Screen SPA Control Panel (browser-based, tablet/desktop)
 *
 * Features:
 * - Web scraping interface (target selection)
 * - Outreach templates (vertical-specific)
 * - Campaign management (drag-drop workflows)
 * - Follow-up sequences (automated)
 * - Lead tracking (conversion metrics)
 * - Booking integration (accepts leads into system)
 * - AI-assisted card interactions
 */
class TitanHelloPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('titan-hello')
            ->path('titan-hello')
            ->brandName('TITAN HELLO — Lead Gen')
            ->colors([
                'primary' => Color::Orange,
            ])
            ->login()
            ->discoverResources(
                in: app_path('Filament/TitanHello/Resources'),
                for: 'App\\Filament\\TitanHello\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/TitanHello/Pages'),
                for: 'App\\Filament\\TitanHello\\Pages'
            )
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/TitanHello/Widgets'),
                for: 'App\\Filament\\TitanHello\\Widgets'
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
