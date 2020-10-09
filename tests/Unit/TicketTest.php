<?php

namespace Tests\Unit;

use App\Models\Concert;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function ticket_can_be_released()
    {
        $concert = Concert::factory()->published()->create(['ticket_price' => 3250])->addTickets(1);
        $this->assertEquals(1, $concert->ticketsRemaining());

        $order = $concert->orderTickets('jane@example.com', 1);
        $this->assertEquals(0, $concert->ticketsRemaining());

        //$concert->refresh();
        $ticket = $order->tickets()->first();
        $this->assertNotNull($ticket);

        $ticket->release();

        $concert->refresh();
        $this->assertNull($ticket->order_id);
        $this->assertEquals(1, $concert->ticketsRemaining());
    }

    /**
     * @test
     */
    public function can_reserve_a_ticket()
    {
        $ticket = Ticket::factory()->create();
        $this->assertNull($ticket->reserved_at);

        $ticket->reserve();

        $this->assertNotNull($ticket->fresh()->reserved_at);
    }
}
