<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Models\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    /**
     * @test
     */
    public function customer_can_purchase_concert_tickets()
    {
        $paymentGateway = new FakePaymentGateway();
        $concert = Concert::factory()->published()->create(['ticket_price' => 3250]);

        $this->postJson("/concerts/{$concert->id}/purchase-ticket", [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $paymentGateway->getValidTestToken(),
        ]);

        $this->assertEquals(3250, $paymentGateway->totalCharges());

        $order = $concert->orders->where('email', 'john@example.com')->first();

        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets->count());
        $this->assertEquals(3, $order->tickets->count());
    }
}
