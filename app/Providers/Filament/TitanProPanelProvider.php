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
 * TITAN PRO — Admin Dashboard + Analytics SPA Control Panel
 *
 * Audience: Leadership / business owners
 * Type: Full-Screen Dashboard App (React SPA with D3/Recharts visualizations)
 *
 * Features:
 * - KPI dashboards (real-time metrics)
 * - Vertical-specific modules (ManagedPremises, Assets, etc.)
 * - Intervention radar (AI alerts)
 * - Multi-site overview
 * - Team performance analysis
 * - Financial reporting
 * - Compliance tracking
 * - AI-assisted card interactions (revenue trends, completion rates, utilization, satisfaction, invoices)
 */
class TitanProPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('titan-pro')
            ->path('titan-pro')
            ->brandName('TITAN PRO — Dashboard')
            ->colors([
                'primary' => Color::Violet,
            ])
            ->login()
            ->discoverResources(
                in: app_path('Filament/TitanPro/Resources'),
                for: 'App\\Filament\\TitanPro\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/TitanPro/Pages'),
                for: 'App\\Filament\\TitanPro\\Pages'
            )
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/TitanPro/Widgets'),
                for: 'App\\Filament\\TitanPro\\Widgets'
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
