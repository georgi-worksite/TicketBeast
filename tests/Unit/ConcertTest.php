<?php

namespace Tests\Unit;

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
}
