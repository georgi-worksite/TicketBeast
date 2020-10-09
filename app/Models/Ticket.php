<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Ticket extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $dates = ['craeted_at'];

    public function scopeAvailable($query)
    {
        return $query->whereNull('order_id')->whereNull('reserved_at');
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

    public function reserve()
    {
        $this->update(['reserved_at' => Carbon::now()]);
    }

    public function getPriceAttribute()
    {
        return $this->concert->ticket_price;
    }
}
