<?php

namespace Modules\TitanVault\Filament\Plugin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\TitanVault\Filament\Resources\AttachmentResource;

class TitanVaultPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'titan-vault';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            AttachmentResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
