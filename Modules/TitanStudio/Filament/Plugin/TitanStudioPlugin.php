<?php

namespace Modules\TitanStudio\Filament\Plugin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\TitanStudio\Filament\Resources\CmsPageResource;

class TitanStudioPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'titan-studio';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            CmsPageResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
