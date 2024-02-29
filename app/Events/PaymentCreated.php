<?php

namespace App\Events;

use App\Payment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class PaymentCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param publicPayment $payment
     *
     * @return void
     */
    public function __construct(public Payment $payment)
    {
    }
}
