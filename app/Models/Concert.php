<?php

namespace App\Models;

use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->hasMany(Order::class);
    }

    /**
     * @return HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function ticketsRemaining()
    {
        return $this->tickets()->available()->count();
    }

    public function addTickets(int $ticketQuantity)
    {
        // Creating tickets
        foreach (range(1, $ticketQuantity) as $index) {
            $this->tickets()->create([]);
        }
    }

    /**
     * @return Order
     */
    public function orderTickets($email, $ticketQuantity)
    {
        $tickets = $this->tickets()->available()->take($ticketQuantity)->get();
        if ($tickets->count() < $ticketQuantity) {
            throw new NotEnoughTicketsException();
        }

        $order = $this->orders()->create(['email' => $email]);

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }
}
