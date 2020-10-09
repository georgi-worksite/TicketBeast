<?php

namespace Tests\Unit;

use App\Models\Concert;
use App\Models\Order;
use App\Models\Ticket;
use App\Reservation;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function creating_an_order_from_tickets_email_and_amount()
    {
        $concert = Concert::factory()->published()->create()->addTickets(5);
        $tickets = $concert->findTickets(2);

        $order = Order::forTickets($tickets, 'jane@example.com', 2400);

        $this->assertNotNull($order);
        $this->assertNotNull($concert->hasOrderFor('jane@example.com'));
        $this->assertEquals(2, $order->ticketQuantity());
        $this->assertEquals(2400, $order->amount);
    }


    /**
     * @test
     */
    public function converting_to_an_array()
    {
        $concert = Concert::factory()->published()->create(['ticket_price' => 3250])->addTickets(3);
        $order = $concert->orderTickets('jane@example.com', 3);

        $results = $order->toArray();

        $this->assertEquals([
            'email' => 'jane@example.com',
            'ticket_quantity' => 3,
            'amount' => 9750,
        ], $results);
    }
}
