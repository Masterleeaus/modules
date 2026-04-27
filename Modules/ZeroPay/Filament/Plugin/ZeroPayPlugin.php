<?php

namespace Modules\ZeroPay\Filament\Plugin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\ZeroPay\Filament\Pages\BASHelperPage;
use Modules\ZeroPay\Filament\Pages\CashflowDashboardPage;
use Modules\ZeroPay\Filament\Pages\CollectionsQueuePage;
use Modules\ZeroPay\Filament\Pages\MarginAnalysisPage;
use Modules\ZeroPay\Filament\Pages\OverdueInvoicesPage;
use Modules\ZeroPay\Filament\Pages\ReminderSchedulePage;
use Modules\ZeroPay\Filament\Pages\XeroSyncPage;
use Modules\ZeroPay\Filament\Resources\EstimateResource;
use Modules\ZeroPay\Filament\Resources\InvoiceResource;
use Modules\ZeroPay\Filament\Resources\PaymentResource;
use Modules\ZeroPay\Filament\Widgets\CashStateWidget;
use Modules\ZeroPay\Filament\Widgets\MarginWarningWidget;
use Modules\ZeroPay\Filament\Widgets\OutstandingWidget;
use Modules\ZeroPay\Filament\Widgets\OverdueRiskWidget;
use Modules\ZeroPay\Filament\Widgets\PaymentPipelineWidget;

class ZeroPayPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'zero-pay';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                EstimateResource::class,
                InvoiceResource::class,
                PaymentResource::class,
            ])
            ->pages([
                CashflowDashboardPage::class,
                OverdueInvoicesPage::class,
                CollectionsQueuePage::class,
                ReminderSchedulePage::class,
                XeroSyncPage::class,
                BASHelperPage::class,
                MarginAnalysisPage::class,
            ])
            ->widgets([
                CashStateWidget::class,
                OutstandingWidget::class,
                OverdueRiskWidget::class,
                PaymentPipelineWidget::class,
                MarginWarningWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
