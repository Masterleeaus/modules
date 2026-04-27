<?php

namespace Modules\GroundZero\Tests\Feature;

use Modules\GroundZero\Events\DispatchBoardUpdated;
use Modules\GroundZero\Events\JobDispatched;
use Modules\GroundZero\Events\TechnicianArrived;
use Modules\GroundZero\Events\TechnicianLeft;
use Modules\GroundZero\Filament\Pages\DispatchBoardPage;
use Modules\GroundZero\Filament\Plugin\GroundZeroPlugin;
use Modules\GroundZero\Filament\Widgets\DispatchStatsWidget;
use Modules\GroundZero\Providers\FilamentServiceProvider;
use Modules\GroundZero\Providers\GroundZeroServiceProvider;
use Modules\GroundZero\Services\DispatchBroadcaster;
use Modules\GroundZero\Services\DispatchService;
use Modules\GroundZero\Services\ETACalculatorService;
use Modules\GroundZero\Services\GeofenceService;
use Modules\GroundZero\Services\RouteOptimiserService;
use Tests\TestCase;

/**
 * Verifies that the GroundZero module is fully wired: providers, services,
 * events, plugin, pages, and widgets are all present and coherent.
 */
class GroundZeroModuleTest extends TestCase
{
    // ── Plugin ────────────────────────────────────────────────────────────────

    /** @test */
    public function plugin_id_is_correct(): void
    {
        $this->assertSame('ground-zero', (new GroundZeroPlugin())->getId());
    }

    /** @test */
    public function plugin_make_returns_instance(): void
    {
        $this->assertInstanceOf(GroundZeroPlugin::class, GroundZeroPlugin::make());
    }

    // ── Filament page ─────────────────────────────────────────────────────────

    /** @test */
    public function dispatch_board_page_slug_is_correct(): void
    {
        $prop = (new \ReflectionClass(DispatchBoardPage::class))->getProperty('slug');
        $prop->setAccessible(true);
        $this->assertSame('ground-zero/dispatch', $prop->getValue(null));
    }

    /** @test */
    public function dispatch_board_page_navigation_group_is_operations(): void
    {
        $prop = (new \ReflectionClass(DispatchBoardPage::class))->getProperty('navigationGroup');
        $prop->setAccessible(true);
        $this->assertSame('Operations', $prop->getValue(null));
    }

    /** @test */
    public function dispatch_board_page_includes_stats_widget(): void
    {
        $page    = new DispatchBoardPage();
        $widgets = $page->getWidgets();
        $this->assertContains(DispatchStatsWidget::class, $widgets);
    }

    // ── Widgets ───────────────────────────────────────────────────────────────

    /** @test */
    public function dispatch_stats_widget_is_stats_overview(): void
    {
        $this->assertInstanceOf(
            \Filament\Widgets\StatsOverviewWidget::class,
            new DispatchStatsWidget(),
        );
    }

    // ── Services ──────────────────────────────────────────────────────────────

    /** @test */
    public function dispatch_service_can_be_resolved(): void
    {
        $this->assertInstanceOf(DispatchService::class, app(DispatchService::class));
    }

    /** @test */
    public function route_optimiser_service_can_be_resolved(): void
    {
        $this->assertInstanceOf(RouteOptimiserService::class, app(RouteOptimiserService::class));
    }

    /** @test */
    public function eta_calculator_service_can_be_resolved(): void
    {
        $this->assertInstanceOf(ETACalculatorService::class, app(ETACalculatorService::class));
    }

    /** @test */
    public function geofence_service_can_be_resolved(): void
    {
        $this->assertInstanceOf(GeofenceService::class, app(GeofenceService::class));
    }

    /** @test */
    public function dispatch_broadcaster_can_be_resolved(): void
    {
        $this->assertInstanceOf(DispatchBroadcaster::class, app(DispatchBroadcaster::class));
    }

    // ── Events ────────────────────────────────────────────────────────────────

    /** @test */
    public function dispatch_board_updated_broadcast_as_is_correct(): void
    {
        $event = new DispatchBoardUpdated(1, 'test_action', []);
        $this->assertSame('board.updated', $event->broadcastAs());
    }

    /** @test */
    public function dispatch_board_updated_broadcast_channel_includes_org_id(): void
    {
        $event = new DispatchBoardUpdated(42, 'test_action', []);
        $channel = $event->broadcastOn();
        $this->assertStringContainsString('42', $channel->name);
    }

    /** @test */
    public function dispatch_board_updated_broadcast_with_contains_action_and_payload(): void
    {
        $event = new DispatchBoardUpdated(1, 'job_reassigned', ['job_id' => 7]);
        $data  = $event->broadcastWith();
        $this->assertSame('job_reassigned', $data['action']);
        $this->assertSame(['job_id' => 7], $data['payload']);
    }

    // ── GeofenceService unit tests ─────────────────────────────────────────────

    /** @test */
    public function geofence_detects_position_inside_radius(): void
    {
        $service = new GeofenceService();

        // Two coordinates ~150 m apart (well within 200 m radius).
        $inside = $service->isInsideGeofence(
            -33.86785, 151.20732,   // origin
            -33.86920, 151.20732,   // ~150 m south
        );

        $this->assertTrue($inside);
    }

    /** @test */
    public function geofence_detects_position_outside_radius(): void
    {
        $service = new GeofenceService();

        // Two coordinates ~1.5 km apart (outside 200 m radius).
        $outside = $service->isInsideGeofence(
            -33.86785, 151.20732,   // origin
            -33.88136, 151.20732,   // ~1.5 km south
        );

        $this->assertFalse($outside);
    }

    /** @test */
    public function haversine_distance_is_accurate(): void
    {
        $service = new GeofenceService();

        // Sydney Opera House → Sydney Harbour Bridge: ~1.2 km
        $dist = $service->haversineDistanceMetres(
            -33.85674, 151.21518,
            -33.85232, 151.21062,
        );

        $this->assertGreaterThan(500, $dist);
        $this->assertLessThan(2000, $dist);
    }

    // ── RouteOptimiserService: no-API-key fallback ────────────────────────────

    /** @test */
    public function route_optimiser_returns_null_without_api_key(): void
    {
        $service = new RouteOptimiserService('');
        $result  = $service->optimise([-33.86, 151.20], ['Sydney, NSW', 'Parramatta, NSW']);
        $this->assertNull($result);
    }

    /** @test */
    public function route_optimiser_returns_null_for_empty_destinations(): void
    {
        $service = new RouteOptimiserService('fake-key');
        $result  = $service->optimise([-33.86, 151.20], []);
        $this->assertNull($result);
    }

    // ── ETACalculatorService: no-API-key fallback ─────────────────────────────

    /** @test */
    public function eta_calculator_returns_null_without_api_key(): void
    {
        $service = new ETACalculatorService('');
        $result  = $service->calculateEtaSeconds([-33.86, 151.20], 'Parramatta, NSW');
        $this->assertNull($result);
    }

    /** @test */
    public function eta_sms_window_returns_false_without_api_key(): void
    {
        $service = new ETACalculatorService('');
        $this->assertFalse($service->isWithinSmsWindow([-33.86, 151.20], 'Parramatta, NSW'));
    }

    // ── Module JSON ───────────────────────────────────────────────────────────

    /** @test */
    public function module_json_lists_service_provider(): void
    {
        $path    = __DIR__ . '/../../module.json';
        $decoded = json_decode(file_get_contents($path), true);

        $this->assertContains(
            GroundZeroServiceProvider::class,
            $decoded['providers'],
        );
    }
}
