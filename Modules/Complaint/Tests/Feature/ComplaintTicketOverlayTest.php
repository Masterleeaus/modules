<?php

namespace Modules\Complaint\Tests\Feature;

use App\Models\Ticket;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Feature tests verifying that the complaint overlay on the tickets table
 * works correctly (ticket_category, booking linkage, refund validation).
 */
class ComplaintTicketOverlayTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: A ticket can be saved with ticket_category = 'complaint'.
     */
    public function test_ticket_saved_with_complaint_category(): void
    {
        $ticket = Ticket::factory()->create([
            'ticket_category' => 'complaint',
            'complaint_source' => 'client',
            'requires_investigation' => false,
        ]);

        $this->assertDatabaseHas('tickets', [
            'id'               => $ticket->id,
            'ticket_category'  => 'complaint',
            'complaint_source' => 'client',
        ]);
    }

    /**
     * Test: The default ticket_category is 'support' so existing tickets are unaffected.
     */
    public function test_existing_tickets_default_to_support_category(): void
    {
        $ticket = Ticket::factory()->create();

        // ticket_category defaults to 'support'; existing tickets not broken
        $this->assertDatabaseHas('tickets', [
            'id'              => $ticket->id,
            'ticket_category' => 'support',
        ]);
    }

    /**
     * Test: A complaint ticket can be linked to a booking via booking_id.
     */
    public function test_booking_linked_complaint_has_booking_id(): void
    {
        $bookingId = 42; // hypothetical booking UUID/int

        $ticket = Ticket::factory()->create([
            'ticket_category' => 'complaint',
            'booking_id'      => $bookingId,
            'service_date'    => '2026-04-01',
        ]);

        $this->assertDatabaseHas('tickets', [
            'id'              => $ticket->id,
            'ticket_category' => 'complaint',
            'booking_id'      => $bookingId,
            'service_date'    => '2026-04-01',
        ]);
    }

    /**
     * Test: A complaint ticket can exist without a booking (booking_id nullable).
     */
    public function test_complaint_without_booking_is_valid(): void
    {
        $ticket = Ticket::factory()->create([
            'ticket_category' => 'complaint',
            'booking_id'      => null,
        ]);

        $this->assertDatabaseHas('tickets', [
            'id'              => $ticket->id,
            'ticket_category' => 'complaint',
            'booking_id'      => null,
        ]);
    }

    /**
     * Test: Resolution fields can be set on a complaint ticket.
     */
    public function test_complaint_resolution_fields_saved(): void
    {
        $ticket = Ticket::factory()->create([
            'ticket_category' => 'complaint',
            'resolution_type' => 'reclean',
            'refund_amount'   => null,
            'resolved_at'     => now()->toDateTimeString(),
        ]);

        $this->assertDatabaseHas('tickets', [
            'id'              => $ticket->id,
            'resolution_type' => 'reclean',
        ]);
    }

    /**
     * Test: The scopeComplaints() scope returns only complaint-category tickets.
     */
    public function test_scope_complaints_filters_correctly(): void
    {
        Ticket::factory()->create(['ticket_category' => 'support']);
        Ticket::factory()->create(['ticket_category' => 'complaint']);
        Ticket::factory()->create(['ticket_category' => 'feedback']);

        $complaints = Ticket::complaints()->get();

        $this->assertCount(1, $complaints);
        $this->assertEquals('complaint', $complaints->first()->ticket_category);
    }

    /**
     * Test: The scopeForBooking() scope returns only complaints for a given booking.
     */
    public function test_scope_for_booking_returns_linked_complaints(): void
    {
        Ticket::factory()->create(['ticket_category' => 'complaint', 'booking_id' => 10]);
        Ticket::factory()->create(['ticket_category' => 'complaint', 'booking_id' => 20]);
        Ticket::factory()->create(['ticket_category' => 'support',   'booking_id' => 10]);

        $linked = Ticket::forBooking(10)->get();

        $this->assertCount(1, $linked);
        $this->assertEquals(10, $linked->first()->booking_id);
        $this->assertEquals('complaint', $linked->first()->ticket_category);
    }
}
