<?php

namespace Modules\CleanQuality\Tests\Feature;

use Tests\TestCase;
use Modules\CleanQuality\Filament\Plugin\CleanQualityPlugin;
use Modules\CleanQuality\Filament\Resources\InspectionResource;
use Modules\CleanQuality\Filament\Resources\QualityCheckResource;
use Modules\CleanQuality\Filament\Widgets\QualityScoreboard;
use Modules\CleanQuality\Filament\Pages\CleanQualityDashboard;
use Modules\CleanQuality\Filament\Resources\InspectionResource\Pages\ListInspections;
use Modules\CleanQuality\Filament\Resources\InspectionResource\Pages\ViewInspection;
use Modules\CleanQuality\Filament\Resources\QualityCheckResource\Pages\ListQualityChecks;
use Modules\CleanQuality\Filament\Resources\QualityCheckResource\Pages\ViewQualityCheck;

/**
 * Verifies that the CleanQuality Filament surface is fully wired:
 * plugin, resources, page classes, and widget all present and coherent.
 */
class CleanQualityFilamentSurfaceTest extends TestCase
{
    // ── Plugin ────────────────────────────────────────────────────────────────

    /** @test */
    public function plugin_id_is_correct(): void
    {
        $this->assertSame('clean-quality', (new CleanQualityPlugin())->getId());
    }

    /** @test */
    public function plugin_make_returns_instance(): void
    {
        $this->assertInstanceOf(CleanQualityPlugin::class, CleanQualityPlugin::make());
    }

    // ── InspectionResource ────────────────────────────────────────────────────

    /** @test */
    public function inspection_resource_table_method_exists(): void
    {
        $this->assertTrue(method_exists(InspectionResource::class, 'table'));
    }

    /** @test */
    public function inspection_resource_infolist_method_exists(): void
    {
        $this->assertTrue(method_exists(InspectionResource::class, 'infolist'));
    }

    /** @test */
    public function inspection_resource_get_pages_returns_index_and_view(): void
    {
        $pages = InspectionResource::getPages();

        $this->assertArrayHasKey('index', $pages);
        $this->assertArrayHasKey('view', $pages);
    }

    /** @test */
    public function inspection_resource_navigation_group_is_quality(): void
    {
        $prop = (new \ReflectionClass(InspectionResource::class))
            ->getProperty('navigationGroup');
        $prop->setAccessible(true);
        $value = $prop->getValue();
        $this->assertSame('Quality', $value);
    }

    /** @test */
    public function inspection_resource_model_is_inspection(): void
    {
        $this->assertSame(
            \Modules\CleanQuality\Entities\Inspection::class,
            InspectionResource::getModel()
        );
    }

    /** @test */
    public function inspection_resource_can_create_is_false(): void
    {
        // InspectionResource is intentionally read-only through Filament.
        $this->assertFalse(InspectionResource::canCreate());
    }

    // ── InspectionResource Pages ──────────────────────────────────────────────

    /** @test */
    public function list_inspections_resource_matches(): void
    {
        $prop = (new \ReflectionClass(ListInspections::class))
            ->getProperty('resource');
        $prop->setAccessible(true);
        $this->assertSame(InspectionResource::class, $prop->getValue());
    }

    /** @test */
    public function view_inspection_resource_matches(): void
    {
        $prop = (new \ReflectionClass(ViewInspection::class))
            ->getProperty('resource');
        $prop->setAccessible(true);
        $this->assertSame(InspectionResource::class, $prop->getValue());
    }

    // ── QualityCheckResource ──────────────────────────────────────────────────

    /** @test */
    public function quality_check_resource_table_method_exists(): void
    {
        $this->assertTrue(method_exists(QualityCheckResource::class, 'table'));
    }

    /** @test */
    public function quality_check_resource_infolist_method_exists(): void
    {
        $this->assertTrue(method_exists(QualityCheckResource::class, 'infolist'));
    }

    /** @test */
    public function quality_check_resource_get_pages_returns_index_and_view(): void
    {
        $pages = QualityCheckResource::getPages();

        $this->assertArrayHasKey('index', $pages);
        $this->assertArrayHasKey('view', $pages);
    }

    /** @test */
    public function quality_check_resource_navigation_group_is_quality(): void
    {
        $prop = (new \ReflectionClass(QualityCheckResource::class))
            ->getProperty('navigationGroup');
        $prop->setAccessible(true);
        $value = $prop->getValue();
        $this->assertSame('Quality', $value);
    }

    /** @test */
    public function quality_check_resource_model_is_qc_record(): void
    {
        $this->assertSame(
            \Modules\CleanQuality\Entities\QcRecord::class,
            QualityCheckResource::getModel()
        );
    }

    /** @test */
    public function quality_check_resource_can_create_is_false(): void
    {
        $this->assertFalse(QualityCheckResource::canCreate());
    }

    // ── QualityCheckResource Pages ────────────────────────────────────────────

    /** @test */
    public function list_quality_checks_resource_matches(): void
    {
        $prop = (new \ReflectionClass(ListQualityChecks::class))
            ->getProperty('resource');
        $prop->setAccessible(true);
        $this->assertSame(QualityCheckResource::class, $prop->getValue());
    }

    /** @test */
    public function view_quality_check_resource_matches(): void
    {
        $prop = (new \ReflectionClass(ViewQualityCheck::class))
            ->getProperty('resource');
        $prop->setAccessible(true);
        $this->assertSame(QualityCheckResource::class, $prop->getValue());
    }

    // ── Dashboard Page ────────────────────────────────────────────────────────

    /** @test */
    public function dashboard_navigation_group_is_quality(): void
    {
        $prop = (new \ReflectionClass(CleanQualityDashboard::class))
            ->getProperty('navigationGroup');
        $prop->setAccessible(true);
        $this->assertSame('Quality', $prop->getValue());
    }

    /** @test */
    public function dashboard_slug_is_clean_quality_dashboard(): void
    {
        $prop = (new \ReflectionClass(CleanQualityDashboard::class))
            ->getProperty('slug');
        $prop->setAccessible(true);
        $this->assertSame('clean-quality/dashboard', $prop->getValue());
    }

    /** @test */
    public function dashboard_includes_quality_scoreboard_widget(): void
    {
        // Instantiate via reflection to call getWidgets without full Livewire boot.
        $page = new CleanQualityDashboard();
        $widgets = $page->getWidgets();

        $this->assertContains(QualityScoreboard::class, $widgets);
    }

    // ── Widget ────────────────────────────────────────────────────────────────

    /** @test */
    public function quality_scoreboard_view_path_is_set(): void
    {
        $prop = (new \ReflectionClass(QualityScoreboard::class))
            ->getProperty('view');
        $prop->setAccessible(true);
        $value = $prop->getValue(new QualityScoreboard());
        $this->assertSame('clean_quality::filament.widgets.quality-scoreboard', $value);
    }
}
