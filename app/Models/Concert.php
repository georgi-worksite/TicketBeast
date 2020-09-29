<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $dates = ['date'];

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
}
