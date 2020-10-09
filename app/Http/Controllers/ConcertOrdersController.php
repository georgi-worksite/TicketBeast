<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Exceptions\NotEnoughTicketsException;
use App\Models\Concert;
use App\Models\Order;
use App\Reservation;
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
        $order = null;
        $concert = Concert::published()->findOrFail($concertId);

        $validatedData = $request->validate([
            'email' => 'required|email',
            'ticket_quantity' => 'required|int|min:1',
            'payment_token' => 'required',
        ]);

        try {
            $tickets = $concert->reserveTickets(request('ticket_quantity'));
            $reservation = new Reservation($tickets);

            $this->paymentGateway->charge(
                $reservation->totalCost(),
                request('payment_token')
            );

            $order = Order::forTickets(
                $tickets,
                request('email'),
                $reservation->totalCost()
            );

            return response()->json($order, 201);
        } catch (PaymentFailedException $e) {
            return response()->json([$e], 422);
        } catch (NotEnoughTicketsException $e) {
            return response()->json([$e], 422);
        }
    }
}
