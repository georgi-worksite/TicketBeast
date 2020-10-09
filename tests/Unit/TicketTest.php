<?php

namespace Tests\Unit;

use App\Models\Concert;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function ticket_can_be_released()
    {
        $ticket = Ticket::factory()->reserved()->create();
        $this->assertNotNull($ticket->reserved_at);

        $ticket->release();

        $this->assertNull($ticket->fresh()->reserved_at);
    }

    /**
     * @test
     */
    public function can_reserve_a_ticket()
    {
        $ticket = Ticket::factory()->create();
        $this->assertNull($ticket->reserved_at);

        $ticket->reserve();

        $this->assertNotNull($ticket->fresh()->reserved_at);
    }
}
