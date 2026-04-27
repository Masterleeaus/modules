<?php

namespace Modules\GroundZero\Filament\Plugin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\GroundZero\Filament\Pages\DispatchBoardPage;
use Modules\GroundZero\Filament\Widgets\DispatchStatsWidget;

class GroundZeroPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'ground-zero';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                DispatchBoardPage::class,
            ])
            ->widgets([
                DispatchStatsWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
