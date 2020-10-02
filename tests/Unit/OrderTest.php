<?php

namespace Tests\Unit;

use App\Models\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function tickets_are_released_when_order_is_cancelled()
    {
        $concert = Concert::factory()->published()->create();
        $concert->addTickets(5);
        $order = $concert->orderTickets('jane@example.com',5);
        $this->assertEquals(5,$concert->ticketsRemaining());
    }
}
