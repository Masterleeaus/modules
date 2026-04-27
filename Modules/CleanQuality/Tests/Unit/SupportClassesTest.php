<?php

namespace Modules\CleanQuality\Tests\Unit;

use Tests\TestCase;
use Modules\CleanQuality\Support\InspectionPermissions;
use Modules\CleanQuality\Support\InspectionRoutes;
use Modules\CleanQuality\Support\Enums\InspectionStatus;
use Modules\CleanQuality\Entities\QcRecord;

class SupportClassesTest extends TestCase
{
    /** @test */
    public function constants_load(): void
    {
        $this->assertTrue(class_exists(InspectionRoutes::class));
        $this->assertTrue(class_exists(InspectionPermissions::class));
        $this->assertTrue(class_exists(InspectionStatus::class));
    }

    /** @test */
    public function inspection_routes_has_three_entries(): void
    {
        $this->assertCount(3, InspectionRoutes::all());
    }

    /** @test */
    public function inspection_routes_contains_expected_names(): void
    {
        $routes = InspectionRoutes::all();

        $this->assertContains(InspectionRoutes::SCHEDULES_INDEX,  $routes);
        $this->assertContains(InspectionRoutes::INSPECTIONS_INDEX, $routes);
        $this->assertContains(InspectionRoutes::RECURRING_INDEX,   $routes);
    }

    /** @test */
    public function inspection_status_defines_expected_states(): void
    {
        $this->assertSame('pending',       InspectionStatus::PENDING);
        $this->assertSame('in_progress',   InspectionStatus::IN_PROGRESS);
        $this->assertSame('passed',        InspectionStatus::PASSED);
        $this->assertSame('failed',        InspectionStatus::FAILED);
        $this->assertSame('reclean_booked', InspectionStatus::RECLEAN_BOOKED);
    }

    /** @test */
    public function qc_record_statuses_constant_is_non_empty_array(): void
    {
        $this->assertIsArray(QcRecord::STATUSES);
        $this->assertNotEmpty(QcRecord::STATUSES);
        $this->assertContains('pass', QcRecord::STATUSES);
        $this->assertContains('fail', QcRecord::STATUSES);
        $this->assertContains('pending', QcRecord::STATUSES);
    }
}

