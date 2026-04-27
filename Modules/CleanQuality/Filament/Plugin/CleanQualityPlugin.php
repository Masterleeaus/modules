<?php

namespace Modules\CleanQuality\Filament\Plugin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\CleanQuality\Filament\Pages\CleanQualityDashboard;
use Modules\CleanQuality\Filament\Resources\InspectionResource;
use Modules\CleanQuality\Filament\Resources\QualityCheckResource;
use Modules\CleanQuality\Filament\Widgets\QualityScoreboard;

class CleanQualityPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'clean-quality';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                InspectionResource::class,
                QualityCheckResource::class,
            ])
            ->pages([
                CleanQualityDashboard::class,
            ])
            ->widgets([
                QualityScoreboard::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}


