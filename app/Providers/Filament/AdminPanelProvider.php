<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\JobStatsOverview;
use App\Models\Job;
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
use SolutionForest\FilamentHeaderSelect\Components\HeaderSelect;
use SolutionForest\FilamentHeaderSelect\HeaderSelectPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->brandName('TITAN ZERO — Admin')
            ->colors([
                'primary' => Color::Slate,
            ])
            ->login()
            ->resources([
                \App\Filament\Resources\OrganizationSettingResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                JobStatsOverview::class,
            ])
            ->plugin(
                HeaderSelectPlugin::make()
                    ->selects([
                        HeaderSelect::make('job_status')
                            ->label('Job Status')
                            ->options(Job::statuses())
                            ->placeholder('Filter by status')
                            ->icon('heroicon-o-funnel'),
                    ])
            )
            ->plugin(
                \Modules\Accountings\Filament\Plugin\AccountingsPlugin::make()
            )
            ->plugin(\Modules\WorksuiteWorkOrders\Filament\Plugin\WorksuitePlugin::make())
            ->plugin(\Modules\ZeroFuss\Filament\Plugin\ZeroFussPlugin::make())
            ->plugin(\Modules\ZeroPay\Filament\Plugin\ZeroPayPlugin::make())
            ->plugin(\Modules\HRCore\Filament\Plugin\HRCorePlugin::make())
            ->plugin(\Modules\GroundZero\Filament\Plugin\GroundZeroPlugin::make())
            ->plugin(\Modules\TitanVault\Filament\Plugin\TitanVaultPlugin::make())
            ->plugin(\Modules\TitanStudio\Filament\Plugin\TitanStudioPlugin::make())
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
