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
 * ZEROPAY — Zero-Fee Payments + AI Follow-up PWA
 *
 * Audience: Customers paying + admin managing collections
 * Type: PWA — installable on phone / web, payment integration
 *
 * Features:
 * - Payment link sending (SMS, email, portal)
 * - Multiple payment methods (PayID, bank, card)
 * - Zero transaction fees (company side)
 * - AI-powered late payment follow-up sequences
 * - Payment attempt tracking
 * - Reconciliation assistance
 * - AI follow-up automation (Day 3 SMS → Day 7 email → Day 14 call script → Day 21 plan → Day 30 escalation)
 */
class ZeroPayPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('zeropay')
            ->path('zeropay')
            ->brandName('ZEROPAY — Payments')
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->login()
            ->discoverResources(
                in: app_path('Filament/ZeroPay/Resources'),
                for: 'App\\Filament\\ZeroPay\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/ZeroPay/Pages'),
                for: 'App\\Filament\\ZeroPay\\Pages'
            )
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/ZeroPay/Widgets'),
                for: 'App\\Filament\\ZeroPay\\Widgets'
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
