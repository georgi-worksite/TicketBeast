<?php

namespace App;

use Illuminate\Support\Collection;

class Reservation
{
    protected Collection $tickets;
    protected int $amount;

    function __construct(Collection $tickets)
    {
        $this->tickets = $tickets;
        $this->amount = $tickets->sum('price');
    }

    public function totalCost()
    {
        return $this->amount;
    }

    public function cancel()
    {
        foreach ($this->tickets as $ticket ) {
            $ticket->release();
        }
    }
}
