<?php

namespace Tests\Feature;

use App\Models\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ViewConcertListingTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_view_a_published_concert_listing()
    {
        $concert = Concert::factory()->published()->create([
            'title' => 'The Red Cord',
            'subtitle' => 'with Animosity and lethargy',
            'date' => Carbon::parse('December 13, 2020 8:00pm'),
            'ticket_price' => 3250,
            'venu' => 'The Mosh Pit',
            'venu_address' => '123 Example Lane',
            'city' => 'Burlington',
            'state' => 'ON',
            'zip' => 'L89R7T',
            'additional' => 'For tickets, call (555) 555-5555.',
        ]);

        $response = $this->get('/concerts/'.$concert->id);

        $response->assertOk();

        $response->assertSeeText('The Red Cord');
        $response->assertSeeText('with Animosity and lethargy');
        $response->assertSeeText('December 13, 2020');
        $response->assertSeeText('8:00pm');
        $response->assertSeeText('32.50');
        $response->assertSeeText('The Mosh Pit');
        $response->assertSeeText('123 Example Lane');
        $response->assertSeeText('Burlington');
        $response->assertSeeText('ON');
        $response->assertSeeText('L89R7T');
        $response->assertSeeText('For tickets, call (555) 555-5555.');
    }

    /**
     * @test
     */
    public function user_cannot_view_unpublished_concert_listings()
    {
        $concert = Concert::factory()->unpublished()->create();

        $response = $this->get('/concerts/'.$concert->id);
        $response->assertNotFound();
    }
}
