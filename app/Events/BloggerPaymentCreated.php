<?php

namespace App\Events;

use App\Blogger;
use App\BloggerPayment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class BloggerPaymentCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $blogger;

    public $payment;

    public $status;

    public function __construct(Blogger $blogger, BloggerPayment $blogger_payment, $status)
    {
        $this->blogger = $blogger;
        $this->payment = $blogger_payment;
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
