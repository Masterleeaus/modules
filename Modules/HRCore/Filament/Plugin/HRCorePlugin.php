<?php

namespace Modules\HRCore\Filament\Plugin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\HRCore\Filament\Resources\TeamMemberResource;

class HRCorePlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'hr-core';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            TeamMemberResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
