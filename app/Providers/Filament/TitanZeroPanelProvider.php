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
use TitanZero\FilamentChatbot\Filament\ChatbotPlugin;
/**
 * TITAN ZERO — Vertical-Specific AI Training PWA
 *
 * Audience: Team members needing vertical training
 * Type: PWA — installable on phone / web, LLM integration
 *
 * Features:
 * - Vertical-specific AI training (context-aware)
 * - Document generation (bond packs, incident reports, SWMS)
 * - Procedural memory (learns your business)
 * - Optimization recommendations
 * - Compliance guidance
 * - Best practices per vertical
 * - Conversational interface
 */
class TitanZeroPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('titan-zero')
            ->path('titan-zero')
            ->brandName('TITAN ZERO — AI Training')
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->login()
            ->discoverResources(
                in: app_path('Filament/TitanZero/Resources'),
                for: 'App\\Filament\\TitanZero\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/TitanZero/Pages'),
                for: 'App\\Filament\\TitanZero\\Pages'
            )
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/TitanZero/Widgets'),
                for: 'App\\Filament\\TitanZero\\Widgets'
            )
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->when(
                class_exists(\TitanZero\FilamentChatbot\Filament\ChatbotPlugin::class),
                fn ($panel) => $panel->plugin(\TitanZero\FilamentChatbot\Filament\ChatbotPlugin::make())
            )
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
