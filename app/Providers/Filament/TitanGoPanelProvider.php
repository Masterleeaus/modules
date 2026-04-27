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
 * TITAN GO — Field Staff Chatbot PWA
 *
 * Audience: Field technicians / cleaning teams
 * Type: PWA (Progressive Web App) — installable on phone, offline-first
 *
 * Features:
 * - Conversational interface (chatbot style)
 * - Today's jobs (pushed from GROUND ZERO)
 * - GPS navigation to next job
 * - Follow checklists (conversational)
 * - Upload proof photos (context-aware prompts)
 * - Completion signature
 * - Offline execution (syncs when online)
 * - AI photo quality analysis
 */
class TitanGoPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('titan-go')
            ->path('titan-go')
            ->brandName('TITAN GO — Field')
            ->colors([
                'primary' => Color::Green,
            ])
            ->login()
            ->discoverResources(
                in: app_path('Filament/TitanGo/Resources'),
                for: 'App\\Filament\\TitanGo\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/TitanGo/Pages'),
                for: 'App\\Filament\\TitanGo\\Pages'
            )
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/TitanGo/Widgets'),
                for: 'App\\Filament\\TitanGo\\Widgets'
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
