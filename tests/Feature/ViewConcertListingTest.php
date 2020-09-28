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
    public function user_can_view_concert_listing()
    {
        $concert = Concert::create([
            'title' => 'The Red Cord',
            'subtitle' => 'with Animosity and lethargy',
            'date' => Carbon::parse('December 13, 2020 8:00pm'),
            'ticket_price' => 3250,
            'venu' => 'THe Mosh Pit',
            'venu_address' => '123 Example Lane',
            'city' => 'Burlington',
            'state' => 'ON',
            'zip' => 'L89R7T',
            'additional' => 'For tickets, call (555) 555-5555.'
        ]);

        $view = $this->view('/concerts/'.$concert->id);

        $view->assertSeeText('The Red Cord');
        $view->assertSeeText('with Animosity and lethargy');
        $view->assertSeeText('December 13, 2020');
        $view->assertSeeText('8:00pm');
        $view->assertSeeText(3250);
        $view->assertSeeText('THe Mosh Pit');
        $view->assertSeeText('123 Example Lane');
        $view->assertSeeText('Burlington');
        $view->assertSeeText('ON');
        $view->assertSeeText('L89R7T');
        $view->assertSeeText('For tickets, call (555) 555-5555.');
    }
}
