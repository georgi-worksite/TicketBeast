<?php

namespace App;

use Illuminate\Support\Collection;

class Reservation
{
    protected Collection $tickets;
    protected int $amount;

    public function __construct(Collection $tickets)
    {
        $this->$tickets = $tickets;
        $this->amount = $tickets->sum('price');
    }

    public function totalCost()
    {
        return $this->amount;
    }
}
