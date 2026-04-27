<?php

namespace Modules\CleanQuality\Tests\Feature;

use Tests\TestCase;

/**
 * Verifies that CleanQuality blade views are resolvable under all
 * registered namespace aliases: inspection, quality_control, clean_quality.
 */
class TitanLinksPanelRendersTest extends TestCase
{
    /** @test */
    public function panel_view_exists_under_inspection_namespace(): void
    {
        // The ServiceProvider registers views under 'inspection' namespace.
        $this->assertTrue(
            view()->exists('inspection::partials.titan-links'),
            'View inspection::partials.titan-links not found.'
        );
    }

    /** @test */
    public function panel_view_exists_under_clean_quality_namespace(): void
    {
        $this->assertTrue(
            view()->exists('clean_quality::partials.titan-links'),
            'View clean_quality::partials.titan-links not found.'
        );
    }

    /** @test */
    public function scoreboard_widget_view_exists(): void
    {
        $this->assertTrue(
            view()->exists('clean_quality::filament.widgets.quality-scoreboard'),
            'Widget view clean_quality::filament.widgets.quality-scoreboard not found.'
        );
    }

    /** @test */
    public function dashboard_page_view_exists(): void
    {
        $this->assertTrue(
            view()->exists('clean_quality::filament.pages.dashboard'),
            'Page view clean_quality::filament.pages.dashboard not found.'
        );
    }
}

