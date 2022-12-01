<?php

namespace App\Events;

use App\PaymentReceipt;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $paymentReceipt;

    public function __construct(PaymentReceipt $paymentReceipt)
    {
        $this->paymentReceipt = $paymentReceipt;
    }
}
