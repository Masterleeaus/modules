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
 * TITAN SOLO — Solo Operator Mode PWA
 *
 * Audience: Solo technicians / independent operators
 * Type: PWA (simplified) — installable on phone (primary) / web
 *
 * Features:
 * - Today's jobs (map view primary)
 * - Quick job lookup
 * - Simplified checklists
 * - Fast photo capture
 * - Quick invoice creation
 * - Simple scheduling view
 * - Income tracking
 * - Chatbot-style interface ("What's next?", "Navigate", "Ready?", "Done")
 * - Mobile-first, minimal screens, no multi-team management
 */
class TitanSoloPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('titan-solo')
            ->path('titan-solo')
            ->brandName('TITAN SOLO — Solo Operator')
            ->colors([
                'primary' => Color::Slate,
            ])
            ->login()
            ->discoverResources(
                in: app_path('Filament/TitanSolo/Resources'),
                for: 'App\\Filament\\TitanSolo\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/TitanSolo/Pages'),
                for: 'App\\Filament\\TitanSolo\\Pages'
            )
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/TitanSolo/Widgets'),
                for: 'App\\Filament\\TitanSolo\\Widgets'
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
