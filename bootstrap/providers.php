<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\Filament\TitanHelloPanelProvider::class,
    App\Providers\Filament\GroundZeroPanelProvider::class,
    App\Providers\Filament\TitanGoPanelProvider::class,
    App\Providers\Filament\ZeroFussPanelProvider::class,
    App\Providers\Filament\TitanProPanelProvider::class,
    App\Providers\Filament\TitanZeroPanelProvider::class,
    App\Providers\Filament\ZeroPayPanelProvider::class,
    App\Providers\Filament\TitanStudioPanelProvider::class,
    App\Providers\Filament\TitanSoloPanelProvider::class,
    App\Providers\FortifyServiceProvider::class,
    Modules\GroundZero\Providers\GroundZeroServiceProvider::class,
    // Only registered when Telescope is installed (dev environments only)
    ...(class_exists(\Laravel\Telescope\TelescopeApplicationServiceProvider::class)
        ? [App\Providers\TelescopeServiceProvider::class]
        : []),
];
