<?php

namespace Tests\Unit;

use App\Models\Concert;
use App\Reservation;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    /**
     * @test
     */
    public function can_reserve_tickets()
    {
        $tickets = collect([
            (object)['price'=>1200],
            (object)['price'=>1200],
            (object)['price'=>1200],
        ]);

        $reservation = new Reservation($tickets);
        $this->assertEquals(3600, $reservation->totalCost());
    }
}
