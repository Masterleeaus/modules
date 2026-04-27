<?php

namespace Modules\ZeroFuss\Filament\Plugin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\ZeroFuss\Filament\Resources\CustomerResource;
use Modules\ZeroFuss\Filament\Resources\PropertyResource;

class ZeroFussPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'zero-fuss';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            CustomerResource::class,
            PropertyResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
