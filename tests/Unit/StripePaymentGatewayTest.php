<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Stripe\StripeClient;
use Tests\TestCase;

class StripePaymentGatewayTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function charges_with_a_valid_payment_token()
    {
        // Create a new stripePaymentGateway
        $paymentGateway = new StripePaymentGateway();

        $stripe = new StripeClient('sk_test_51HaRQJGQZ3MdLKsJhwOQUMEGVOeprHShzoOCbRqGaQIHHgYXBgyAmTbjb5gkgzfKxYogujBdaLpAeQ1p6I81HZeo00mhk3E1l4');
        $stripe->charges->create([
            'amount' => 2000,
            'currency' => 'cad',
            'source' => 'tok_amex',
            'description' => 'My First Test Charge (created for API docs)',
        ]);

        // Create a charge from amount using a valid apyament token
        $paymentGateway->charge(2500, 'valida-token');

        // verify that the charge was complete successfully
    }
}
