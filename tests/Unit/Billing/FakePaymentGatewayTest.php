<?php

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    protected FakePaymentGateway $paymentGateway;

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

    /**
     * @test
     */
    public function running_a_fake_payment_gateway_test()
    {
        $callbackRanTimes = 0;
        $this->paymentGateway->beforeFirstCharge(function ($paymentGateway) use (&$callbackRanTimes) {
            $callbackRanTimes++;
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
            $this->assertEquals(2500, $paymentGateway->totalCharges());
        });
        $this->assertEquals(0,$callbackRanTimes);

        $this->paymentGateway->charge(2500, $this->paymentGateway->getValidTestToken());

        $this->assertEquals(1,$callbackRanTimes);
        $this->assertEquals(5000, $this->paymentGateway->totalCharges());
    }
}
