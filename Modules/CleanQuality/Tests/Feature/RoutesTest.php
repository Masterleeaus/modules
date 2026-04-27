<?php

namespace Modules\CleanQuality\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoutesTest extends TestCase
{
    /** @test */
    public function it_registers_expected_route_names(): void
    {
        $this->assertTrue(Route::has('inspection_schedules.index'), 'inspection_schedules.index is missing');
        $this->assertTrue(Route::has('schedule-inspection.index'), 'schedule-inspection.index is missing');
        $this->assertTrue(Route::has('recurring-inspection_schedules.index'), 'recurring-inspection_schedules.index is missing');
        $this->assertTrue(Route::has('inspection-templates.index'), 'inspection-templates.index is missing');
        $this->assertTrue(Route::has('inspection_schedules.set_outcome'), 'inspection_schedules.set_outcome is missing');
        $this->assertTrue(Route::has('inspection_schedules.create_quality_issue'), 'inspection_schedules.create_quality_issue is missing');
    }
}


