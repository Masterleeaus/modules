<?php

namespace Modules\Accountings\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Accountings\Entities\BasPeriod;
use Tests\TestCase;

/**
 * Feature tests for the BasPeriod entity.
 */
class BasPeriodTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('bas_periods')) {
            Schema::create('bas_periods', function ($table) {
                $table->id();
                $table->unsignedBigInteger('organization_id')->index();
                $table->string('period_type', 20)->default('quarterly');
                $table->date('period_start');
                $table->date('period_end');
                $table->decimal('gst_collected', 15, 2)->default(0);
                $table->decimal('gst_paid', 15, 2)->default(0);
                $table->decimal('net_gst', 15, 2)->default(0);
                $table->string('status', 20)->default('draft');
                $table->timestamp('lodged_at')->nullable();
                $table->timestamp('locked_at')->nullable();
                $table->timestamps();
            });
        }
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('bas_periods');
        parent::tearDown();
    }

    /** @test */
    public function bas_period_can_be_created(): void
    {
        $period = BasPeriod::create([
            'organization_id' => 1,
            'period_type'     => 'quarterly',
            'period_start'    => '2025-01-01',
            'period_end'      => '2025-03-31',
            'gst_collected'   => 1500.00,
            'gst_paid'        => 500.00,
            'net_gst'         => 1000.00,
            'status'          => 'draft',
        ]);

        $this->assertNotNull($period->id);
        $this->assertSame('draft', $period->status);
        $this->assertSame('1000.00', (string) $period->net_gst);
    }

    /** @test */
    public function bas_period_is_not_locked_by_default(): void
    {
        $period = BasPeriod::create([
            'organization_id' => 1,
            'period_type'     => 'quarterly',
            'period_start'    => '2025-01-01',
            'period_end'      => '2025-03-31',
            'status'          => 'draft',
        ]);

        $this->assertFalse($period->isLocked());
        $this->assertFalse($period->isLodged());
    }

    /** @test */
    public function bas_period_lodged_status_detected(): void
    {
        $period = BasPeriod::create([
            'organization_id' => 1,
            'period_type'     => 'quarterly',
            'period_start'    => '2025-01-01',
            'period_end'      => '2025-03-31',
            'status'          => 'lodged',
            'lodged_at'       => now(),
        ]);

        $this->assertTrue($period->isLodged());
    }

    /** @test */
    public function bas_period_draft_scope_works(): void
    {
        BasPeriod::create(['organization_id' => 1, 'period_type' => 'quarterly', 'period_start' => '2025-01-01', 'period_end' => '2025-03-31', 'status' => 'draft']);
        BasPeriod::create(['organization_id' => 1, 'period_type' => 'quarterly', 'period_start' => '2025-04-01', 'period_end' => '2025-06-30', 'status' => 'lodged', 'lodged_at' => now()]);

        $drafts = BasPeriod::draft()->get();
        $this->assertCount(1, $drafts);
        $this->assertSame('draft', $drafts->first()->status);
    }
}
