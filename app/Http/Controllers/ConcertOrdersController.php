<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Exceptions\NotEnoughTicketsException;
use App\Models\Concert;
use App\Models\Order;
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
            $reservation = $concert->reserveTickets(request('ticket_quantity'), request('email'));

            $order = $reservation->complete($this->paymentGateway, request('payment_token'));

            return response()->json($order, 201);
        } catch (PaymentFailedException $e) {
            $reservation->cancel();

            return response()->json([$e], 422);
        } catch (NotEnoughTicketsException $e) {
            return response()->json([$e], 422);
        }
    }
}
