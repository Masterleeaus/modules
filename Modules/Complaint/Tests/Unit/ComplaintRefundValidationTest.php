<?php

namespace Modules\Complaint\Tests\Unit;

use App\Models\Ticket;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Unit tests for complaint-specific business rules.
 */
class ComplaintRefundValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Refund amount can be stored and retrieved accurately.
     */
    public function test_refund_amount_stored_with_two_decimal_precision(): void
    {
        $ticket = Ticket::factory()->create([
            'ticket_category' => 'complaint',
            'resolution_type' => 'refund',
            'refund_amount'   => 125.50,
        ]);

        $fresh = Ticket::find($ticket->id);

        $this->assertEquals('125.50', number_format((float) $fresh->refund_amount, 2));
    }

    /**
     * Test: A complaint without a refund resolution has null refund_amount.
     */
    public function test_non_refund_resolution_has_null_refund_amount(): void
    {
        $ticket = Ticket::factory()->create([
            'ticket_category' => 'complaint',
            'resolution_type' => 'apology',
            'refund_amount'   => null,
        ]);

        $this->assertNull($ticket->refund_amount);
    }

    /**
     * Test: requires_investigation defaults to false.
     */
    public function test_requires_investigation_defaults_to_false(): void
    {
        $ticket = Ticket::factory()->create([
            'ticket_category' => 'complaint',
        ]);

        $this->assertFalse((bool) $ticket->requires_investigation);
    }

    /**
     * Test: requires_investigation can be set to true for escalated complaints.
     */
    public function test_requires_investigation_can_be_set_true(): void
    {
        $ticket = Ticket::factory()->create([
            'ticket_category'        => 'complaint',
            'requires_investigation' => true,
        ]);

        $this->assertDatabaseHas('tickets', [
            'id'                     => $ticket->id,
            'requires_investigation' => true,
        ]);
    }
}
