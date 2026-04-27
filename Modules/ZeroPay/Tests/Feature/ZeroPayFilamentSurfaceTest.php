<?php

namespace Modules\ZeroPay\Tests\Feature;

use Modules\ZeroPay\Filament\Pages\BASHelperPage;
use Modules\ZeroPay\Filament\Pages\CashflowDashboardPage;
use Modules\ZeroPay\Filament\Pages\CollectionsQueuePage;
use Modules\ZeroPay\Filament\Pages\MarginAnalysisPage;
use Modules\ZeroPay\Filament\Pages\OverdueInvoicesPage;
use Modules\ZeroPay\Filament\Pages\ReminderSchedulePage;
use Modules\ZeroPay\Filament\Pages\XeroSyncPage;
use Modules\ZeroPay\Filament\Plugin\ZeroPayPlugin;
use Modules\ZeroPay\Filament\Resources\EstimateResource;
use Modules\ZeroPay\Filament\Resources\EstimateResource\Pages\CreateEstimate;
use Modules\ZeroPay\Filament\Resources\EstimateResource\Pages\EditEstimate;
use Modules\ZeroPay\Filament\Resources\EstimateResource\Pages\ListEstimates;
use Modules\ZeroPay\Filament\Resources\InvoiceResource;
use Modules\ZeroPay\Filament\Resources\InvoiceResource\Pages\CreateInvoice;
use Modules\ZeroPay\Filament\Resources\InvoiceResource\Pages\EditInvoice;
use Modules\ZeroPay\Filament\Resources\InvoiceResource\Pages\ListInvoices;
use Modules\ZeroPay\Filament\Resources\PaymentResource;
use Modules\ZeroPay\Filament\Resources\PaymentResource\Pages\CreatePayment;
use Modules\ZeroPay\Filament\Resources\PaymentResource\Pages\EditPayment;
use Modules\ZeroPay\Filament\Resources\PaymentResource\Pages\ListPayments;
use Modules\ZeroPay\Filament\Widgets\CashStateWidget;
use Modules\ZeroPay\Filament\Widgets\MarginWarningWidget;
use Modules\ZeroPay\Filament\Widgets\OutstandingWidget;
use Modules\ZeroPay\Filament\Widgets\OverdueRiskWidget;
use Modules\ZeroPay\Filament\Widgets\PaymentPipelineWidget;
use Tests\TestCase;

/**
 * Verifies that the ZeroPay Filament surface is fully wired:
 * plugin, resources, page classes, and widgets are all present and coherent.
 */
class ZeroPayFilamentSurfaceTest extends TestCase
{
    // ── Plugin ────────────────────────────────────────────────────────────────

    /** @test */
    public function plugin_id_is_correct(): void
    {
        $this->assertSame('zero-pay', (new ZeroPayPlugin())->getId());
    }

    /** @test */
    public function plugin_make_returns_instance(): void
    {
        $this->assertInstanceOf(ZeroPayPlugin::class, ZeroPayPlugin::make());
    }

    // ── EstimateResource ──────────────────────────────────────────────────────

    /** @test */
    public function estimate_resource_model_is_estimate(): void
    {
        $this->assertSame(\App\Models\Estimate::class, EstimateResource::getModel());
    }

    /** @test */
    public function estimate_resource_slug_is_scoped(): void
    {
        $prop = (new \ReflectionClass(EstimateResource::class))->getProperty('slug');
        $prop->setAccessible(true);
        $this->assertSame('zero-pay/estimates', $prop->getValue());
    }

    /** @test */
    public function estimate_resource_navigation_group_is_estimates(): void
    {
        $prop = (new \ReflectionClass(EstimateResource::class))->getProperty('navigationGroup');
        $prop->setAccessible(true);
        $this->assertSame('Estimates', $prop->getValue());
    }

    /** @test */
    public function estimate_resource_get_pages_has_index_create_edit(): void
    {
        $pages = EstimateResource::getPages();
        $this->assertArrayHasKey('index', $pages);
        $this->assertArrayHasKey('create', $pages);
        $this->assertArrayHasKey('edit', $pages);
    }

    /** @test */
    public function list_estimates_resource_matches(): void
    {
        $prop = (new \ReflectionClass(ListEstimates::class))->getProperty('resource');
        $prop->setAccessible(true);
        $this->assertSame(EstimateResource::class, $prop->getValue());
    }

    /** @test */
    public function create_estimate_resource_matches(): void
    {
        $prop = (new \ReflectionClass(CreateEstimate::class))->getProperty('resource');
        $prop->setAccessible(true);
        $this->assertSame(EstimateResource::class, $prop->getValue());
    }

    /** @test */
    public function edit_estimate_resource_matches(): void
    {
        $prop = (new \ReflectionClass(EditEstimate::class))->getProperty('resource');
        $prop->setAccessible(true);
        $this->assertSame(EstimateResource::class, $prop->getValue());
    }

    // ── InvoiceResource ───────────────────────────────────────────────────────

    /** @test */
    public function invoice_resource_model_is_invoice(): void
    {
        $this->assertSame(\App\Models\Invoice::class, InvoiceResource::getModel());
    }

    /** @test */
    public function invoice_resource_slug_is_scoped(): void
    {
        $prop = (new \ReflectionClass(InvoiceResource::class))->getProperty('slug');
        $prop->setAccessible(true);
        $this->assertSame('zero-pay/invoices', $prop->getValue());
    }

    /** @test */
    public function invoice_resource_navigation_group_is_invoices(): void
    {
        $prop = (new \ReflectionClass(InvoiceResource::class))->getProperty('navigationGroup');
        $prop->setAccessible(true);
        $this->assertSame('Invoices', $prop->getValue());
    }

    /** @test */
    public function invoice_resource_get_pages_has_index_create_edit(): void
    {
        $pages = InvoiceResource::getPages();
        $this->assertArrayHasKey('index', $pages);
        $this->assertArrayHasKey('create', $pages);
        $this->assertArrayHasKey('edit', $pages);
    }

    /** @test */
    public function list_invoices_resource_matches(): void
    {
        $prop = (new \ReflectionClass(ListInvoices::class))->getProperty('resource');
        $prop->setAccessible(true);
        $this->assertSame(InvoiceResource::class, $prop->getValue());
    }

    /** @test */
    public function create_invoice_resource_matches(): void
    {
        $prop = (new \ReflectionClass(CreateInvoice::class))->getProperty('resource');
        $prop->setAccessible(true);
        $this->assertSame(InvoiceResource::class, $prop->getValue());
    }

    /** @test */
    public function edit_invoice_resource_matches(): void
    {
        $prop = (new \ReflectionClass(EditInvoice::class))->getProperty('resource');
        $prop->setAccessible(true);
        $this->assertSame(InvoiceResource::class, $prop->getValue());
    }

    // ── PaymentResource ───────────────────────────────────────────────────────

    /** @test */
    public function payment_resource_model_is_payment(): void
    {
        $this->assertSame(\App\Models\Payment::class, PaymentResource::getModel());
    }

    /** @test */
    public function payment_resource_slug_is_scoped(): void
    {
        $prop = (new \ReflectionClass(PaymentResource::class))->getProperty('slug');
        $prop->setAccessible(true);
        $this->assertSame('zero-pay/payments', $prop->getValue());
    }

    /** @test */
    public function payment_resource_navigation_group_is_payments(): void
    {
        $prop = (new \ReflectionClass(PaymentResource::class))->getProperty('navigationGroup');
        $prop->setAccessible(true);
        $this->assertSame('Payments', $prop->getValue());
    }

    /** @test */
    public function payment_resource_get_pages_has_index_create_edit(): void
    {
        $pages = PaymentResource::getPages();
        $this->assertArrayHasKey('index', $pages);
        $this->assertArrayHasKey('create', $pages);
        $this->assertArrayHasKey('edit', $pages);
    }

    /** @test */
    public function list_payments_resource_matches(): void
    {
        $prop = (new \ReflectionClass(ListPayments::class))->getProperty('resource');
        $prop->setAccessible(true);
        $this->assertSame(PaymentResource::class, $prop->getValue());
    }

    /** @test */
    public function create_payment_resource_matches(): void
    {
        $prop = (new \ReflectionClass(CreatePayment::class))->getProperty('resource');
        $prop->setAccessible(true);
        $this->assertSame(PaymentResource::class, $prop->getValue());
    }

    /** @test */
    public function edit_payment_resource_matches(): void
    {
        $prop = (new \ReflectionClass(EditPayment::class))->getProperty('resource');
        $prop->setAccessible(true);
        $this->assertSame(PaymentResource::class, $prop->getValue());
    }

    // ── Pages ─────────────────────────────────────────────────────────────────

    /** @test */
    public function cashflow_dashboard_slug_is_correct(): void
    {
        $prop = (new \ReflectionClass(CashflowDashboardPage::class))->getProperty('slug');
        $prop->setAccessible(true);
        $this->assertSame('zero-pay/cashflow', $prop->getValue());
    }

    /** @test */
    public function cashflow_dashboard_navigation_group_is_cashflow(): void
    {
        $prop = (new \ReflectionClass(CashflowDashboardPage::class))->getProperty('navigationGroup');
        $prop->setAccessible(true);
        $this->assertSame('Cashflow', $prop->getValue());
    }

    /** @test */
    public function cashflow_dashboard_includes_all_widgets(): void
    {
        $page    = new CashflowDashboardPage();
        $widgets = $page->getWidgets();

        $this->assertContains(CashStateWidget::class, $widgets);
        $this->assertContains(OutstandingWidget::class, $widgets);
        $this->assertContains(OverdueRiskWidget::class, $widgets);
        $this->assertContains(PaymentPipelineWidget::class, $widgets);
        $this->assertContains(MarginWarningWidget::class, $widgets);
    }

    /** @test */
    public function overdue_invoices_slug_is_correct(): void
    {
        $prop = (new \ReflectionClass(OverdueInvoicesPage::class))->getProperty('slug');
        $prop->setAccessible(true);
        $this->assertSame('zero-pay/overdue-invoices', $prop->getValue());
    }

    /** @test */
    public function collections_queue_slug_is_correct(): void
    {
        $prop = (new \ReflectionClass(CollectionsQueuePage::class))->getProperty('slug');
        $prop->setAccessible(true);
        $this->assertSame('zero-pay/collections-queue', $prop->getValue());
    }

    /** @test */
    public function reminder_schedule_slug_is_correct(): void
    {
        $prop = (new \ReflectionClass(ReminderSchedulePage::class))->getProperty('slug');
        $prop->setAccessible(true);
        $this->assertSame('zero-pay/reminder-schedule', $prop->getValue());
    }

    /** @test */
    public function xero_sync_slug_is_correct(): void
    {
        $prop = (new \ReflectionClass(XeroSyncPage::class))->getProperty('slug');
        $prop->setAccessible(true);
        $this->assertSame('zero-pay/xero-sync', $prop->getValue());
    }

    /** @test */
    public function bas_helper_slug_is_correct(): void
    {
        $prop = (new \ReflectionClass(BASHelperPage::class))->getProperty('slug');
        $prop->setAccessible(true);
        $this->assertSame('zero-pay/bas-helper', $prop->getValue());
    }

    /** @test */
    public function margin_analysis_slug_is_correct(): void
    {
        $prop = (new \ReflectionClass(MarginAnalysisPage::class))->getProperty('slug');
        $prop->setAccessible(true);
        $this->assertSame('zero-pay/margin-analysis', $prop->getValue());
    }

    // ── Widgets ───────────────────────────────────────────────────────────────

    /** @test */
    public function cash_state_widget_is_stats_overview(): void
    {
        $this->assertInstanceOf(
            \Filament\Widgets\StatsOverviewWidget::class,
            new CashStateWidget()
        );
    }

    /** @test */
    public function outstanding_widget_is_stats_overview(): void
    {
        $this->assertInstanceOf(
            \Filament\Widgets\StatsOverviewWidget::class,
            new OutstandingWidget()
        );
    }

    /** @test */
    public function overdue_risk_widget_is_stats_overview(): void
    {
        $this->assertInstanceOf(
            \Filament\Widgets\StatsOverviewWidget::class,
            new OverdueRiskWidget()
        );
    }

    /** @test */
    public function payment_pipeline_widget_is_stats_overview(): void
    {
        $this->assertInstanceOf(
            \Filament\Widgets\StatsOverviewWidget::class,
            new PaymentPipelineWidget()
        );
    }

    /** @test */
    public function margin_warning_widget_is_stats_overview(): void
    {
        $this->assertInstanceOf(
            \Filament\Widgets\StatsOverviewWidget::class,
            new MarginWarningWidget()
        );
    }
}
