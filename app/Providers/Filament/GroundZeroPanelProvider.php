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
 * GROUND ZERO — Dispatch Control SPA Control Panel
 *
 * Audience: Dispatch managers / schedulers
 * Type: Full-Screen SPA Control Panel (browser-based, tablet/desktop, real-time)
 *
 * Features:
 * - Calendar view (drag-drop jobs)
 * - Route optimization visualization
 * - Technician assignment (skill-based)
 * - Real-time dispatch (push to TITAN GO)
 * - Status tracking (live job updates)
 * - Multi-site management (switch between sites)
 * - AI-assisted card interactions (unassigned jobs, route optimization, utilization, risk)
 */
class GroundZeroPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('ground-zero')
            ->path('ground-zero')
            ->brandName('GROUND ZERO — Dispatch')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->login()
            ->discoverResources(
                in: app_path('Filament/GroundZero/Resources'),
                for: 'App\\Filament\\GroundZero\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/GroundZero/Pages'),
                for: 'App\\Filament\\GroundZero\\Pages'
            )
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/GroundZero/Widgets'),
                for: 'App\\Filament\\GroundZero\\Widgets'
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
