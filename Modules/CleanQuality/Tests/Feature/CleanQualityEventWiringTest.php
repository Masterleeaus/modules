<?php

namespace Modules\CleanQuality\Tests\Feature;

use Tests\TestCase;
use Modules\CleanQuality\Providers\EventServiceProvider;
use Modules\CleanQuality\Events\InspectionCompleted;
use Modules\CleanQuality\Events\QualityScoreUpdated;
use Modules\CleanQuality\Events\RecleanAuthorised;
use Modules\CleanQuality\Listeners\InspectionCompletedListener;
use Modules\CleanQuality\Listeners\QualityScoreUpdatedListener;
use Modules\CleanQuality\Listeners\RecleanAuthorisedListener;

/**
 * Verifies that the CleanQuality EventServiceProvider registers all
 * required event-listener mappings and observer pairs.
 */
class CleanQualityEventWiringTest extends TestCase
{
    private function getListenMap(): array
    {
        $provider = new EventServiceProvider(app());
        $prop = new \ReflectionProperty(EventServiceProvider::class, 'listen');
        $prop->setAccessible(true);
        return $prop->getValue($provider);
    }

    /** @test */
    public function inspection_completed_is_wired_to_listener(): void
    {
        $map = $this->getListenMap();

        $this->assertArrayHasKey(InspectionCompleted::class, $map,
            'InspectionCompleted event is not registered in EventServiceProvider.'
        );

        $this->assertContains(
            InspectionCompletedListener::class,
            $map[InspectionCompleted::class],
            'InspectionCompletedListener is not registered for InspectionCompleted.'
        );
    }

    /** @test */
    public function quality_score_updated_is_wired_to_listener(): void
    {
        $map = $this->getListenMap();

        $this->assertArrayHasKey(QualityScoreUpdated::class, $map);
        $this->assertContains(QualityScoreUpdatedListener::class, $map[QualityScoreUpdated::class]);
    }

    /** @test */
    public function reclean_authorised_is_wired_to_listener(): void
    {
        $map = $this->getListenMap();

        $this->assertArrayHasKey(RecleanAuthorised::class, $map);
        $this->assertContains(RecleanAuthorisedListener::class, $map[RecleanAuthorised::class]);
    }

    /** @test */
    public function observers_include_schedule_and_recurring_schedule(): void
    {
        $provider = new EventServiceProvider(app());
        $prop = new \ReflectionProperty(EventServiceProvider::class, 'observers');
        $prop->setAccessible(true);
        $observers = $prop->getValue($provider);

        $this->assertArrayHasKey(\Modules\CleanQuality\Entities\Schedule::class, $observers);
        $this->assertArrayHasKey(\Modules\CleanQuality\Entities\RecurringSchedule::class, $observers);
    }
}
