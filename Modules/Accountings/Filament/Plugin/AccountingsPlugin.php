<?php

namespace Modules\Accountings\Filament\Plugin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Accountings\Filament\Pages\BasSummaryPage;
use Modules\Accountings\Filament\Pages\ProfitabilityPage;
use Modules\Accountings\Filament\Pages\XeroConnectionPage;
use Modules\Accountings\Filament\Resources\AccountResource;
use Modules\Accountings\Filament\Resources\JournalEntryResource;

class AccountingsPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'accountings';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                AccountResource::class,
                JournalEntryResource::class,
            ])
            ->pages([
                ProfitabilityPage::class,
                BasSummaryPage::class,
                XeroConnectionPage::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
