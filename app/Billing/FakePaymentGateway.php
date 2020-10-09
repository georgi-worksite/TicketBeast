<?php

namespace App\Billing;

class FakePaymentGateway implements PaymentGateway
{
    private $charges;
    private $beforeFirstCallCallback;

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
        if ($this->beforeFirstCallCallback !== null) {
            $callback = $this->beforeFirstCallCallback;
            $this->beforeFirstCallCallback = null;
            $callback->__invoke($this);
        }
        if ($token !== $this->getValidTestToken()) {
            throw new PaymentFailedException();
        }
        $this->charges[] = $amount;
    }

    public function totalCharges()
    {
        return $this->charges->sum();
    }

    public function beforeFirstCharge($callback)
    {
        $this->beforeFirstCallCallback = $callback;
    }
}
