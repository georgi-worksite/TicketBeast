<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeAvailable($query)
    {
        return $query->whereNull('order_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    public function release()
    {
        $this->order()->dissociate()->save();
    }

    public function getPriceAttribute()
    {
        return $this->concert->ticket_price;
    }
}
