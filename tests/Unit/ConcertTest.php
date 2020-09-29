<?php

namespace Tests\Unit;

use App\Models\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ConcertTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_get_formatted_date()
    {
        $concert = Concert::factory()->create([
            'date' => Carbon::parse('2016-12-01 8:00pm'),
        ]);

        $this->assertEquals('December 1, 2016', $concert->formatted_date);
    }

    /**
     * @test
     */
    public function can_get_formatted_time()
    {
        $concert = Concert::factory()->create([
            'date' => Carbon::parse('2016-12-01 18:00:02'),
        ]);

        $this->assertEquals('6:00pm', $concert->formatted_time);
    }

    /**
     * @test
     */
    public function can_get_formatted_price()
    {
        $concert = Concert::factory()->create([
            'ticket_price' => '3220',
        ]);

        $this->assertEquals('32.20', $concert->formatted_ticket_price);
    }
}
