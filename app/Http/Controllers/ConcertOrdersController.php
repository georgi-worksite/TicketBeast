<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Models\Concert;
use Illuminate\Http\Request;

class ConcertOrdersController extends Controller
{
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    /**
     * Stores new Concert order.
     *
     * @return void
     */
    public function store($concertId, Request $request)
    {
        $concert = Concert::published()->findOrFail($concertId);

        $validatedData = $request->validate([
            'email' => 'required|email',
            'ticket_quantity' => 'required|int|min:1',
            'payment_token' => 'required',
        ]);

        try {
            $this->paymentGateway->charge(request('ticket_quantity') * $concert->ticket_price, request('payment_token'));
            $order = $concert->orderTickets(request('email'), request('ticket_quantity'));

            return response()->json($order, 201);
        } catch (PaymentFailedException $e) {
            return \response()->json([], 422);
        }
    }
}
