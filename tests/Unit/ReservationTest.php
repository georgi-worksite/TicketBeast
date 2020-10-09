<?php

namespace Tests\Unit;

use App\Models\Ticket;
use App\Reservation;
use Mockery;
use Tests\TestCase;

class ReservationTest extends TestCase
{
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

        $reservation = new Reservation($tickets);
        $this->assertEquals(3600, $reservation->totalCost());
    }

    /**
     * @test
     */
    public function reserved_tickets_are_released_when_a_reservation_is_released()
    {
        $this->withoutExceptionHandling();
        $tickets = collect([]);
        foreach (range(0, 2) as $index) {
            $tickets->push(Mockery::spy(Ticket::class));
        }
        $reservation = new Reservation($tickets);

        $reservation->cancel();

        foreach ($tickets as $ticket) {
            $ticket->shouldHaveReceived('release')->once();
        }
    }
}
