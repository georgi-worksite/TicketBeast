<?php

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    protected $paymentGateway;

    public function setUp(): void
    {
        $this->paymentGateway = new FakePaymentGateway();
    }

    /**
     * @test
     */
    public function charges_with_a_valid_payment_token_are_successfull()
    {
        $this->paymentGateway->charge(2500, $this->paymentGateway->getValidTestToken());
        $this->assertEquals(2500, $this->paymentGateway->totalCharges());
    }

    /**
     * @test
     */
    public function charges_with_a_invalid_payment_token_fails()
    {
        try {
            $this->paymentGateway->charge(2500, 'invalid-token');
            $this->assertEquals(2500, $this->paymentGateway->totalCharges());
        } catch (PaymentFailedException $e) {
            $this->assertTrue(true);
            return;
        }
        $this->fail();
    }
}
