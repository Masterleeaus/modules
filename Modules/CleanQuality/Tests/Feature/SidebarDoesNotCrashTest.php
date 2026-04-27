<?php

namespace Modules\CleanQuality\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SidebarDoesNotCrashTest extends TestCase
{
    /** @test */
    public function sidebar_renders_without_missing_routes(): void
    {
        // All routes referenced by CompanyMenuListener must exist so the legacy
        // sidebar never throws a RouteNotFoundException.
        $this->assertTrue(Route::has('inspection_schedules.index'));
        $this->assertTrue(Route::has('recurring-inspection_schedules.index'));
    }

    /** @test */
    public function inspection_route_constants_are_registered(): void
    {
        foreach (\Modules\CleanQuality\Support\InspectionRoutes::all() as $routeName) {
            $this->assertTrue(
                Route::has($routeName),
                "Route constant '{$routeName}' is not registered."
            );
        }
    }
}

