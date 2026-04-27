<?php

namespace Modules\WorksuiteWorkOrders\Filament\Plugin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\WorksuiteWorkOrders\Filament\Resources\JobChecklistItemResource;
use Modules\WorksuiteWorkOrders\Filament\Resources\JobMessageResource;
use Modules\WorksuiteWorkOrders\Filament\Resources\JobResource;
use Modules\WorksuiteWorkOrders\Filament\Resources\JobSupplyUsageResource;
use Modules\WorksuiteWorkOrders\Filament\Resources\JobTypeChecklistItemResource;
use Modules\WorksuiteWorkOrders\Filament\Resources\JobTypeResource;
use Modules\WorksuiteWorkOrders\Filament\Resources\MessageTemplateResource;
use Modules\WorksuiteWorkOrders\Filament\Resources\RecurringJobTemplateResource;

class WorksuitePlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'worksuite-work-orders';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            JobResource::class,
            JobTypeResource::class,
            RecurringJobTemplateResource::class,
            JobChecklistItemResource::class,
            JobSupplyUsageResource::class,
            JobTypeChecklistItemResource::class,
            MessageTemplateResource::class,
            JobMessageResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
