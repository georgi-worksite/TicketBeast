<?php

namespace Tests\Unit;

use App\Billing\FakePaymentGateway;
use App\Models\Concert;
use App\Models\Ticket;
use App\Reservation;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * @test
     */
    public function can_reserve_tickets()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets, 'jane@example.com');
        $this->assertEquals(3600, $reservation->totalCost());
    }

    /**
     * @test
     */
    public function retrieving_the_reservations_tickets()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets, 'jane@example.com');
        $this->assertEquals($tickets, $reservation->tickets());
    }

    /**
     * @test
     */
    public function retrieving_the_customers_email()
    {
        $tickets = collect([]);

        $reservation = new Reservation($tickets, 'jane@example.com');
        $this->assertEquals('jane@example.com', $reservation->email());
    }

    /**
     * @test
     */
    public function reserved_tickets_are_released_when_a_reservation_is_cancelled()
    {
        $tickets = collect([]);
        foreach (range(0, 2) as $index) {
            $tickets->push(Mockery::spy(Ticket::class));
        }
        $reservation = new Reservation($tickets, 'jane@example.com');

        $reservation->cancel();

        foreach ($tickets as $ticket) {
            $ticket->shouldHaveReceived('release')->once();
        }
    }

    /**
     * @test
     */
    public function confirm_a_reservation()
    {
        $concert = Concert::factory()->create(['ticket_price' => 3200]);
        $tickets = Ticket::factory(2)->create(['concert_id' => $concert->id]);
        $reservation = new Reservation($tickets, 'jane@example.com');
        $paymentGateway = new FakePaymentGateway();

        $order = $reservation->complete($paymentGateway, $paymentGateway->getValidTestToken());

        $this->assertNotNull($order);
        $this->assertEquals(2, $order->ticketQuantity());
        $this->assertEquals(6400, $order->amount);
        $this->assertEquals($reservation->totalCost(),$paymentGateway->totalCharges());

        foreach ($tickets as $ticket) {
            //$ticket->shouldHaveReceived('confirm')->once();
        }
    }
}
