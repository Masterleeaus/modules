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
use Guava\Calendar\CalendarPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Kenepa\Banner\BannerPlugin;
use Modules\Accountings\Filament\Plugin\AccountingsPlugin;
use SolutionForest\FilamentHeaderSelect\Components\HeaderSelect;
use SolutionForest\FilamentHeaderSelect\HeaderSelectPlugin;
use TomatoPHP\FilamentInvoices\FilamentInvoicesPlugin;
use TomatoPHP\FilamentPayments\FilamentPaymentsPlugin;

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
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
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
                AccountingsPlugin::make()
            )
            ->plugin(CalendarPlugin::make())
            ->plugin(BannerPlugin::make()->persistsBannersInDatabase())
            ->plugin(FilamentInvoicesPlugin::make())
            ->plugin(FilamentPaymentsPlugin::make())
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
