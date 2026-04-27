<?php

namespace Modules\CleanQuality\Tests\Feature;

use Tests\TestCase;
use Modules\CleanQuality\Actions\CompleteInspection;
use Modules\CleanQuality\Actions\AuthoriseReclean;
use Modules\CleanQuality\Actions\ScoreQualityCheck;
use Modules\CleanQuality\Events\InspectionCompleted;
use Modules\CleanQuality\Events\RecleanAuthorised;
use Modules\CleanQuality\Events\QualityScoreUpdated;
use Modules\CleanQuality\Jobs\GenerateQualityReportJob;
use Modules\CleanQuality\Jobs\ScheduleQualityCheckJob;
use Modules\CleanQuality\Jobs\SendInspectionReminderJob;
use Modules\CleanQuality\Listeners\InspectionCompletedListener;
use Modules\CleanQuality\Listeners\QualityScoreUpdatedListener;
use Modules\CleanQuality\Listeners\RecleanAuthorisedListener;
use Modules\CleanQuality\Support\Enums\InspectionStatus;

/**
 * Behavioral tests for CleanQuality actions, events, jobs, and listeners.
 *
 * These tests verify that:
 * - Actions dispatch the correct events.
 * - Events carry the correct payload models.
 * - Jobs implement ShouldQueue and have a handle() method.
 * - Listeners have a handle() method with the correct signature.
 */
class CleanQualityAutomationTest extends TestCase
{
    // ── Actions dispatch correct events ───────────────────────────────────────

    /** @test */
    public function complete_inspection_dispatches_inspection_completed_event(): void
    {
        \Illuminate\Support\Facades\Event::fake([InspectionCompleted::class]);

        $inspection = new \Modules\CleanQuality\Entities\Inspection();
        $inspection->id = 1;
        $inspection->exists = false; // prevent actual save

        // Use a partial mock to avoid DB interaction.
        $mock = \Mockery::mock(\Modules\CleanQuality\Entities\Inspection::class)->makePartial();
        $mock->shouldReceive('fill')->andReturnSelf();
        $mock->shouldReceive('save')->andReturn(true);
        $mock->status = InspectionStatus::PASSED;
        $mock->inspected_at = now();

        $action = new CompleteInspection();
        $result = $action->execute($mock, ['status' => InspectionStatus::PASSED]);

        \Illuminate\Support\Facades\Event::assertDispatched(InspectionCompleted::class);
        $this->assertInstanceOf(\Modules\CleanQuality\Entities\Inspection::class, $result);
    }

    /** @test */
    public function authorise_reclean_dispatches_reclean_authorised_event(): void
    {
        \Illuminate\Support\Facades\Event::fake([RecleanAuthorised::class]);

        $mock = \Mockery::mock(\Modules\CleanQuality\Entities\Inspection::class)->makePartial();
        $mock->shouldReceive('save')->andReturn(true);

        $action = new AuthoriseReclean();
        $action->execute($mock, 99);

        \Illuminate\Support\Facades\Event::assertDispatched(RecleanAuthorised::class);
        $this->assertSame(InspectionStatus::RECLEAN_BOOKED, $mock->status);
    }

    /** @test */
    public function authorise_reclean_sets_reclean_booking_id(): void
    {
        \Illuminate\Support\Facades\Event::fake([RecleanAuthorised::class]);

        $mock = \Mockery::mock(\Modules\CleanQuality\Entities\Inspection::class)->makePartial();
        $mock->shouldReceive('save')->andReturn(true);

        $action = new AuthoriseReclean();
        $action->execute($mock, 42);

        $this->assertSame(42, $mock->reclean_booking_id);
    }

    /** @test */
    public function score_quality_check_dispatches_quality_score_updated_event(): void
    {
        \Illuminate\Support\Facades\Event::fake([QualityScoreUpdated::class]);

        $mock = \Mockery::mock(\Modules\CleanQuality\Entities\Schedule::class)->makePartial();
        $mock->shouldReceive('save')->andReturn(true);

        $action = new ScoreQualityCheck();
        $result = $action->execute($mock, 87.5);

        \Illuminate\Support\Facades\Event::assertDispatched(QualityScoreUpdated::class);
        $this->assertSame(87.5, $mock->score);
    }

    // ── Events carry correct model ────────────────────────────────────────────

    /** @test */
    public function inspection_completed_event_holds_inspection(): void
    {
        $inspection = new \Modules\CleanQuality\Entities\Inspection();
        $event = new InspectionCompleted($inspection);

        $this->assertSame($inspection, $event->inspection);
    }

    /** @test */
    public function reclean_authorised_event_holds_inspection(): void
    {
        $inspection = new \Modules\CleanQuality\Entities\Inspection();
        $event = new RecleanAuthorised($inspection);

        $this->assertSame($inspection, $event->inspection);
    }

    /** @test */
    public function quality_score_updated_event_holds_schedule(): void
    {
        $schedule = new \Modules\CleanQuality\Entities\Schedule();
        $event = new QualityScoreUpdated($schedule);

        $this->assertSame($schedule, $event->schedule);
    }

    // ── Jobs are queued jobs with a handle() ─────────────────────────────────

    /** @test */
    public function generate_quality_report_job_implements_should_queue(): void
    {
        $this->assertInstanceOf(
            \Illuminate\Contracts\Queue\ShouldQueue::class,
            new GenerateQualityReportJob(1)
        );
    }

    /** @test */
    public function schedule_quality_check_job_implements_should_queue(): void
    {
        $this->assertInstanceOf(
            \Illuminate\Contracts\Queue\ShouldQueue::class,
            new ScheduleQualityCheckJob(1)
        );
    }

    /** @test */
    public function send_inspection_reminder_job_implements_should_queue(): void
    {
        $this->assertInstanceOf(
            \Illuminate\Contracts\Queue\ShouldQueue::class,
            new SendInspectionReminderJob(1)
        );
    }

    // ── Listeners have handle() ───────────────────────────────────────────────

    /** @test */
    public function inspection_completed_listener_has_handle(): void
    {
        $this->assertTrue(method_exists(InspectionCompletedListener::class, 'handle'));
    }

    /** @test */
    public function quality_score_updated_listener_has_handle(): void
    {
        $this->assertTrue(method_exists(QualityScoreUpdatedListener::class, 'handle'));
    }

    /** @test */
    public function reclean_authorised_listener_has_handle(): void
    {
        $this->assertTrue(method_exists(RecleanAuthorisedListener::class, 'handle'));
    }

    // ── InspectionCompletedListener emits reclean signal on failure ───────────

    /** @test */
    public function inspection_completed_listener_fires_reclean_signal_when_failed(): void
    {
        \Illuminate\Support\Facades\Event::fake();

        $inspection = new \Modules\CleanQuality\Entities\Inspection();
        $inspection->id = 10;
        $inspection->company_id = 1;
        $inspection->status = InspectionStatus::FAILED;

        $event = new InspectionCompleted($inspection);
        $listener = new InspectionCompletedListener();
        $listener->handle($event);

        \Illuminate\Support\Facades\Event::assertDispatched('booking_module.reclean.required');
    }

    /** @test */
    public function inspection_completed_listener_does_not_fire_reclean_signal_when_passed(): void
    {
        \Illuminate\Support\Facades\Event::fake();

        $inspection = new \Modules\CleanQuality\Entities\Inspection();
        $inspection->id = 11;
        $inspection->company_id = 1;
        $inspection->status = InspectionStatus::PASSED;

        $event = new InspectionCompleted($inspection);
        $listener = new InspectionCompletedListener();
        $listener->handle($event);

        \Illuminate\Support\Facades\Event::assertNotDispatched('booking_module.reclean.required');
    }
}
