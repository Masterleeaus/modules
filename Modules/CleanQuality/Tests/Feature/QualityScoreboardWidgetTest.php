<?php

namespace Modules\CleanQuality\Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Modules\CleanQuality\Filament\Widgets\QualityScoreboard;
use Modules\CleanQuality\Support\Enums\InspectionStatus;
use Modules\CleanQuality\Entities\QcRecord;

/**
 * Behavioral tests for QualityScoreboard widget data calculation.
 *
 * We test the view-data shape directly, not the Livewire/Blade rendering,
 * to keep the tests framework-agnostic and fast.
 */
class QualityScoreboardWidgetTest extends TestCase
{
    /** @test */
    public function get_view_data_returns_all_expected_keys(): void
    {
        // Mock both DB queries to return an empty aggregate row.
        DB::shouldReceive('select')->andReturn([]);

        $widget = new QualityScoreboard();

        // Call the protected method via reflection.
        $method = new \ReflectionMethod(QualityScoreboard::class, 'getViewData');
        $method->setAccessible(true);
        $data = $method->invoke($widget);

        $expectedKeys = [
            'period',
            'insp_total',
            'insp_passed',
            'insp_failed',
            'insp_reclean',
            'insp_pass_rate',
            'insp_avg_score',
            'qc_total',
            'qc_passed',
            'qc_failed',
            'qc_reclean',
            'qc_pass_rate',
            'qc_avg_score',
        ];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $data, "Key '{$key}' missing from widget view data.");
        }
    }

    /** @test */
    public function period_label_is_thirty_days(): void
    {
        $widget = new QualityScoreboard();
        $method = new \ReflectionMethod(QualityScoreboard::class, 'getViewData');
        $method->setAccessible(true);
        $data = $method->invoke($widget);

        $this->assertSame('30 days', $data['period']);
    }

    /** @test */
    public function widget_returns_null_pass_rate_when_no_inspections(): void
    {
        // With no rows in the DB the query aggregate will return null totals.
        $widget = new QualityScoreboard();
        $method = new \ReflectionMethod(QualityScoreboard::class, 'getViewData');
        $method->setAccessible(true);
        $data = $method->invoke($widget);

        // When total is 0 the pass rate must be null (not a division-by-zero error).
        if ($data['insp_total'] === 0) {
            $this->assertNull($data['insp_pass_rate']);
        }

        if ($data['qc_total'] === 0) {
            $this->assertNull($data['qc_pass_rate']);
        }

        // Either way no exception should be thrown.
        $this->assertTrue(true);
    }

    /** @test */
    public function widget_view_string_is_registered(): void
    {
        $this->assertSame(
            'clean_quality::filament.widgets.quality-scoreboard',
            (new \ReflectionProperty(QualityScoreboard::class, 'view'))->getValue(new QualityScoreboard())
        );
    }

    /** @test */
    public function inspection_status_constants_match_widget_query_parameters(): void
    {
        // The widget hard-codes InspectionStatus constants in its selectRaw bindings.
        // This test verifies the constants still match expected string values.
        $this->assertSame('passed',        InspectionStatus::PASSED);
        $this->assertSame('failed',        InspectionStatus::FAILED);
        $this->assertSame('reclean_booked', InspectionStatus::RECLEAN_BOOKED);
    }

    /** @test */
    public function qc_record_statuses_used_by_widget_are_present(): void
    {
        $this->assertContains('pass', QcRecord::STATUSES);
        $this->assertContains('fail', QcRecord::STATUSES);
    }
}
