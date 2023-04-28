<?php

namespace App\Events;

use App\Vendor;
use App\VendorPayment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class VendorPaymentCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $vendor;

    public $payment;

    public $status;

    public function __construct(Vendor $vendor, VendorPayment $vendor_payment, $status)
    {
        $this->vendor = $vendor;
        $this->payment = $vendor_payment;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
