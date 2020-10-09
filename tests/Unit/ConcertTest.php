<?php

namespace Tests\Unit;

use App\Exceptions\NotEnoughTicketsException;
use App\Models\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ConcertTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function can_get_formatted_date()
    {
        $concert = Concert::factory()->make([
            'date' => Carbon::parse('2016-12-01 8:00pm'),
        ]);

        $this->assertEquals('December 1, 2016', $concert->formatted_date);
    }

    /**
     * @test
     */
    public function can_get_formatted_time()
    {
        $concert = Concert::factory()->make([
            'date' => Carbon::parse('2016-12-01 18:00:02'),
        ]);

        $this->assertEquals('6:00pm', $concert->formatted_time);
    }

    /**
     * @test
     */
    public function can_get_formatted_price()
    {
        $concert = Concert::factory()->make([
            'ticket_price' => '3220',
        ]);

        $this->assertEquals('32.20', $concert->formatted_ticket_price);
    }

    /**
     * @test
     */
    public function concerts_with_a_published_at_dates_are_published()
    {
        $publishedConcertA = Concert::factory()->create([
            'published_at' => Carbon::parse('-2 weeks'),
        ]);

        $publishedConcertB = Concert::factory()->create([
            'published_at' => Carbon::parse('-2 weeks'),
        ]);

        $publishedConcertC = Concert::factory()->create([
            'published_at' => null,
        ]);

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($publishedConcertC));
    }

    /**
     * @test
     */
    public function can_add_tickets()
    {
        $concert = Concert::factory()->create([])->addTickets(10);

        $this->assertEquals(10, $concert->ticketsRemaining());
    }

    /**
     * @test
     */
    public function can_order_concert_tickets()
    {
        $concert = Concert::factory()->create([])->addTickets(10);
        $order = $concert->orderTickets('jane@example.com', 3);

        $this->assertNotNull($order);
        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
    }

    /**
     * @test
     */
    public function cannot_order_more_concert_tickets_than_available()
    {
        $concert = Concert::factory()->create([])->addTickets(10);

        try {
            $order = $concert->orderTickets('jane@example.com', 12);
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('jane@example.com'));
            $this->assertEquals(10, $concert->ticketsRemaining());

            return;
        }
        $this->fail('Order succedded even though not enough tickets available!');
    }

    /**
     * @test
     */
    public function cannot_order_tickets_thats_alreday_purchased()
    {
        $concert = Concert::factory()->create([])->addTickets(10);
        $concert->orderTickets('john@example.com', 5);

        $this->assertEquals(5, $concert->ticketsRemaining());

        try {
            $concert->orderTickets('jane@example.com', 6);
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('jane@example.com'));
            $this->assertEquals(5, $concert->ticketsRemaining());

            return;
        }
        $this->fail('Order succedded even though not enough tickets available!');
    }

    /**
     * @test
     */
    public function tickets_remaining_does_not_inlcude_tickets_associated_with_an_order()
    {
        $concert = Concert::factory()->create([])->addTickets(10);
        $concert->orderTickets('jane@example.com', 3);

        $this->assertEquals(7, $concert->ticketsRemaining());
    }

    /**
     * @test
     */
    public function can_reserve_avilable_tickets()
    {
        /** @var Concert */
        $concert = Concert::factory()->published()->create([])->addTickets(3);
        $this->assertEquals(3, $concert->ticketsRemaining());

        $reservedTickets = $concert->reserveTickets(2);

        $this->assertNotNull($reservedTickets);
        $this->assertEquals(1, $concert->ticketsRemaining());
    }

    /**
     * @test
     */
    public function cannot_reserve_tickets_that_have_alreday_purchased()
    {
        /** @var Concert */
        $concert = Concert::factory()->published()->create([])->addTickets(4);
        $this->assertEquals(4, $concert->ticketsRemaining());
        $concert->orderTickets('john@example.com', 3);
        $this->assertEquals(1, $concert->ticketsRemaining());

        try {
            $reservedTickets = $concert->reserveTickets(2);
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }
        $this->fail("Reserving ticket succeeded even though the tickets were sold.");
    }

    /**
     * @test
     */
    public function cannot_reserve_tickets_that_have_alreday_been_reserved()
    {
        /** @var Concert */
        $concert = Concert::factory()->published()->create([])->addTickets(3);
        $this->assertEquals(3, $concert->ticketsRemaining());
        $concert->reserveTickets(2);
        $this->assertEquals(1, $concert->ticketsRemaining());

        try {
            $reservedTickets = $concert->reserveTickets(2);
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }
        $this->fail("Reserving ticket succeeded even though the tickets were alreday reserved by someone else.");
    }
}
