<?php

namespace Modules\Accountings\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Modules\Accountings\Jobs\XeroSyncJob;
use Modules\Accountings\Services\XeroSyncService;
use Tests\TestCase;

/**
 * Feature tests for the Xero sync workflow.
 *
 * Uses SQLite in-memory (via the testing environment) so no real DB is needed.
 */
class XeroSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a minimal invoices table for testing
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function ($table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->default(1);
                $table->string('invoice_number')->default('INV-001');
                $table->decimal('total', 16, 2)->default(0);
                $table->boolean('exported_to_xero')->default(false);
                $table->string('xero_invoice_id')->nullable();
                $table->timestamps();
            });
        }
    }

    protected function tearDown(): void
    {
        // Drop our test table so it doesn't bleed into other tests
        Schema::dropIfExists('invoices');
        parent::tearDown();
    }

    /** @test */
    public function xero_sync_job_is_dispatched_to_queue(): void
    {
        Queue::fake();

        XeroSyncJob::dispatch(42, 1);

        Queue::assertPushed(XeroSyncJob::class, function (XeroSyncJob $job) {
            return $job->invoiceId === 42 && $job->companyId === 1;
        });
    }

    /** @test */
    public function xero_sync_sets_exported_to_xero_flag(): void
    {
        // Insert a test invoice
        $invoiceId = \Illuminate\Support\Facades\DB::table('invoices')->insertGetId([
            'company_id'       => 1,
            'invoice_number'   => 'INV-TEST-001',
            'total'            => 110.00,
            'exported_to_xero' => false,
            'xero_invoice_id'  => null,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Use a partial mock so we can override postToXero without live Xero credentials
        $service = $this->partialMock(XeroSyncService::class, function ($mock) {
            $mock->shouldAllowMockingProtectedMethods();
            $mock->shouldReceive('getCredentials')->andReturn([
                'tenant_id'     => 'test-tenant',
                'client_id'     => 'test-client',
                'client_secret' => 'test-secret',
            ]);
            $mock->shouldReceive('postToXero')->andReturn('XERO-UUID-1234');
        });

        $result = $service->syncInvoice($invoiceId, 1);

        $this->assertTrue($result);

        $updated = \Illuminate\Support\Facades\DB::table('invoices')->find($invoiceId);
        $this->assertTrue((bool) $updated->exported_to_xero);
        $this->assertSame('XERO-UUID-1234', $updated->xero_invoice_id);
    }

    /** @test */
    public function xero_sync_is_prevented_on_second_attempt(): void
    {
        // Insert an already-exported invoice
        $invoiceId = \Illuminate\Support\Facades\DB::table('invoices')->insertGetId([
            'company_id'       => 1,
            'invoice_number'   => 'INV-ALREADY-EXPORTED',
            'total'            => 110.00,
            'exported_to_xero' => true,
            'xero_invoice_id'  => 'XERO-UUID-EXISTING',
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        $service = $this->partialMock(XeroSyncService::class, function ($mock) {
            // postToXero should never be called for an already-exported invoice
            $mock->shouldAllowMockingProtectedMethods();
            $mock->shouldNotReceive('postToXero');
        });

        $result = $service->syncInvoice($invoiceId, 1);

        $this->assertFalse($result, 'Sync should return false when invoice already exported');

        // Confirm the xero_invoice_id was NOT changed
        $unchanged = \Illuminate\Support\Facades\DB::table('invoices')->find($invoiceId);
        $this->assertSame('XERO-UUID-EXISTING', $unchanged->xero_invoice_id);
    }

    /** @test */
    public function xero_sync_returns_false_when_invoice_not_found(): void
    {
        $service = new XeroSyncService();

        $result = $service->syncInvoice(999999, 1);

        $this->assertFalse($result);
    }
}
