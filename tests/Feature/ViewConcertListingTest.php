<?php

namespace Tests\Feature;

use App\Models\Concert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ViewCourseLIstingTest extends TestCase
{
    /**
     * @test
     */
    public function user_can_view_concert_listing()
    {
        $concert = Concert::create([
            'title' => 'The Red Cord',
            'subtitle' => 'with Animosity and lethargy',
            'date' => Carbon::parse('DEcember 13, 2020 8:00pm'),
            'ticket_price' => 3250,
            'venu' => 'THe Mosh Pit',
            'venu_address' => '123 Example Lane',
            'city' => 'Burlington',
            'state' => 'ON',
            'zip' => 'L89R7T',
            'additional' => 'For tickets, call (555) 555-5555.'
        ]);
        $response = $this->get('/');
        $response = $this->visit('/concerts/'.$concert->id);
        $response->assertStatus(200);
    }
}
