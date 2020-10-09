<?php

namespace App;

use App\Models\Order;
use Illuminate\Support\Collection;

class Reservation
{
    protected Collection $tickets;
    protected int $amount;
    protected string $email;

    public function __construct(Collection $tickets, string $email)
    {
        $this->tickets = $tickets;
        $this->amount = $tickets->sum('price');
        $this->email = $email;
    }

    public function totalCost()
    {
        return $this->amount;
    }

    public function complete($paymentGateway, $validPaymentToken)
    {
        $paymentGateway->charge(
            $this->totalCost(),
            $validPaymentToken
        );

        $order = Order::forTickets(
            $this->tickets(),
            $this->email(),
            $this->totalCost(),
        );

        foreach ($this->tickets() as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    public function cancel()
    {
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }
    }

    public function tickets()
    {
        return $this->tickets;
    }

    public function email()
    {
        return $this->email;
    }
}
