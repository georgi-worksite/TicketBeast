<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Models\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    protected $paymentGateway;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentGateway = new FakePaymentGateway();
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    /**
     * @param mixed $concert_id
     * @param mixed $params
     *
     * @return TestResponse
     */
    protected function orderTickets($concert_id, $params)
    {
        return $this->postJson("/concerts/{$concert_id}/orders", $params);
    }

    /**
     * @test
     */
    public function customer_can_purchase_published_concert_tickets()
    {
        $this->withoutExceptionHandling();

        $concert = Concert::factory()->published()->create(['ticket_price' => 3250])->addTickets(10);

        $response = $this->orderTickets($concert->id, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertCreated();
        $this->assertEquals(9750, $this->paymentGateway->totalCharges());

        $this->assertTrue($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(3, $concert->ordersFor('john@example.com')->first()->tickets()->count());
    }

    /**
     * @test
     */
    public function email_is_required_to_purchase_published_concert_tickets()
    {
        $concert = Concert::factory()->published()->create(['ticket_price' => 3250]);

        $response = $this->orderTickets($concert->id, [
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertJsonValidationErrors('email');
    }

    /**
     * @test
     */
    public function valid_email_is_required_to_purchase_published_concert_tickets()
    {
        $concert = Concert::factory()->published()->create(['ticket_price' => 3250])->addTickets(2);

        $response = $this->orderTickets($concert->id, [
            'email' => 'asdere-rtrtrt',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertJsonValidationErrors('email');
    }

    /**
     * @test
     */
    public function ticket_quantity_is_required_to_purchase_published_concert_tickets()
    {
        $concert = Concert::factory()->published()->create(['ticket_price' => 3250])->addTickets(2);

        $response = $this->orderTickets($concert->id, [
            'email' => 'jane@example.com',
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertJsonValidationErrors('ticket_quantity');
    }

    /**
     * @test
     */
    public function ticket_quantity_should_be_one_or_more_to_purchase_published_concert_tickets()
    {
        $concert = Concert::factory()->published()->create(['ticket_price' => 3250])->addTickets(2);

        $response = $this->orderTickets($concert->id, [
            'email' => 'jane@example.com',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertJsonValidationErrors('ticket_quantity');
    }

    /**
     * @test
     */
    public function payment_token_is_required_for_purchase_published_concert_tickets()
    {
        $concert = Concert::factory()->published()->create(['ticket_price' => 3250])->addTickets(2);

        $response = $this->orderTickets($concert->id, [
            'email' => 'jane@example.com',
            'ticket_quantity' => 0,
        ]);

        $response->assertJsonValidationErrors('payment_token');
        $this->assertFalse($concert->hasOrderFor('jane@example.com'));
    }

    /**
     * @test
     */
    public function valid_payment_token_is_required_for_purchase_published_concert_tickets()
    {
        $concert = Concert::factory()->published()->create(['ticket_price' => 3250])->addTickets(2);

        $response = $this->orderTickets($concert->id, [
            'email' => 'jane@example.com',
            'ticket_quantity' => 1,
            'payment_token' => 'invalid-payment-token',
        ]);

        $response->assertStatus(422);
        $this->assertFalse($concert->hasOrderFor('jane@example.com'));
    }

    /**
     * @test
     */
    public function cannot_purchase_tickets_for_unpublished_concerts()
    {
        $concert = Concert::factory()->unpublished()->create(['ticket_price' => 3250])->addTickets(2);

        $response = $this->orderTickets($concert->id, [
            'email' => 'jane@example.com',
            'ticket_quantity' => 1,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertNotFound();
        $this->assertEquals(0, $concert->orders()->count());
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
    }

    /**
     * @test
     */
    public function cannot_purchase_more_tickets_than_remain_for_published_concerts()
    {
        $this->withoutExceptionHandling();
        $concert = Concert::factory()->published()->create(['ticket_price' => 3250]);
        $concert->addTickets(5);

        $response = $this->orderTickets($concert->id, [
            'email' => 'jane@example.com',
            'ticket_quantity' => 6,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(422);
        $order = $concert->orders()->find(['email' => 'jane@example.com'])->first();
        $this->assertNull($order);
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertEquals(5, $concert->ticketsRemaining());
    }
}
