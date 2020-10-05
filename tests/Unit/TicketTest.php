<?php

namespace Tests\Unit;

use App\Models\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use DatabaseMigrations;

    /**
    * @test
    */
    public function ticket_can_be_released()
    {
        $concert = Concert::factory()->published()->create(['ticket_price'=>3250]);
        $concert->addTickets(1);
        $this->assertEquals(1,$concert->ticketsRemaining());

        $order = $concert->orderTickets('jane@example.com', 1);
        $this->assertEquals(0, $concert->ticketsRemaining());

        //$concert->refresh();
        $ticket = $order->tickets()->first();
        $this->assertNotNull($ticket);

        $ticket->release();


        $concert->refresh();
        $this->assertNull($ticket->order_id);
        $this->assertEquals(1,$concert->ticketsRemaining());
    }
}
