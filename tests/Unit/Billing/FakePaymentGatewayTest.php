<?php

use App\Billing\FakePaymentGateway;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    /**
     *
     * @test
     */
    public function charges_with_a_valid_payment_token_are_successfull()
    {
        $paymentGateway = new FakePaymentGateway();
        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }
}
