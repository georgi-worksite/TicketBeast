<?php

namespace App\Billing;

class FakePaymentGateway implements PaymentGateway
{
    private $charges;

    public function __construct()
    {
        $this->charges = collect([]);
    }

    public function getValidTestToken()
    {
        return 'valid-token-45478';
    }

    public function charge(int $amount, string $token)
    {
        if ($token !== $this->getValidTestToken()) {
            throw new PaymentFailedException();
        }
        $this->charges[] = $amount;
    }

    public function totalCharges()
    {
        return $this->charges->sum();
    }
}
