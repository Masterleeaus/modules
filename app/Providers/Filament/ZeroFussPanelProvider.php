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
 * ZERO FUSS — Customer Portal Chatbot PWA
 *
 * Audience: Customers (property managers, agents, homeowners)
 * Type: PWA — installable on phone / accessible on web
 *
 * Features:
 * - Job status tracking (conversational)
 * - Photo viewing (before/after galleries)
 * - Invoice access (payment options)
 * - Rescheduling requests (conversational)
 * - Issue reporting (auto-escalation)
 * - Upsell recommendations (AI-driven)
 * - Review submission
 * - 100% chatbot interface (zero forms)
 */
class ZeroFussPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('zero-fuss')
            ->path('zero-fuss')
            ->brandName('ZERO FUSS — Customer Portal')
            ->colors([
                'primary' => Color::Cyan,
            ])
            ->login()
            ->discoverResources(
                in: app_path('Filament/ZeroFuss/Resources'),
                for: 'App\\Filament\\ZeroFuss\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/ZeroFuss/Pages'),
                for: 'App\\Filament\\ZeroFuss\\Pages'
            )
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/ZeroFuss/Widgets'),
                for: 'App\\Filament\\ZeroFuss\\Widgets'
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
