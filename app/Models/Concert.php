<?php

namespace App\Models;

use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Concert extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $dates = ['date', 'published_at'];

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    /**
     * Returns formatted date.
     *
     * @return void string
     */
    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }

    /**
     * Returns formatted time.
     *
     * @return void string
     */
    public function getFormattedTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    /**
     * Returns formatted time.
     *
     * @return void string
     */
    public function getFormattedTicketPriceAttribute()
    {
        return number_format($this->ticket_price / 100, 2);
    }

    /**
     * @return HasMany
     */
    public function orders()
    {
        //return $this->hasMany(Order::class);
        return $this->belongsToMany(Order::class, 'tickets');
    }

    /**
     * @return HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * @return int
     */
    public function ticketsRemaining()
    {
        return $this->tickets()->available()->count();
    }

    /**
     * @return Concert
     */
    public function addTickets(int $ticketQuantity)
    {
        // Creating tickets
        foreach (range(1, $ticketQuantity) as $index) {
            $this->tickets()->create([]);
        }

        return $this;
    }

    /**
     * @return Order
     */
    public function orderTickets(string $email, int $ticketQuantity)
    {
        $tickets = $this->findTickets($ticketQuantity);

        return $this->createOrder($email, $tickets);
    }

    /**
     * @return Collection
     */
    public function findTickets(int $quantity)
    {
        $tickets = $this->tickets()->available()->take($quantity)->get();
        if ($tickets->count() < $quantity) {
            throw new NotEnoughTicketsException();
        }

        return $tickets;
    }

    public function reserveTickets(int $ticketQuantity)
    {
        $tickets = $this->findTickets($ticketQuantity)->each(function ($ticket){
            $ticket->reserve();
        });

        return $tickets;
    }

    public function createOrder(string $email, Collection $tickets)
    {
        return Order::forTickets($tickets, $email, $tickets->sum('price'));
    }

    public function hasOrderFor(string $userEmail)
    {
        return $this->orders()->where('email', $userEmail)->count() > 0;
    }

    public function ordersFor(string $userEmail)
    {
        return $this->orders()->where('email', $userEmail)->get();
    }
}
