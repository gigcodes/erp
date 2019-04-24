<?php

namespace App\Mail;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $order;

    public function __construct(Order $order)
    {
      $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      $subject = "New Order # " . $this->order->order_id;

      return $this->from('customercare@sololuxury.co.in')
                  ->bcc('customercare@sololuxury.co.in')
                  ->subject($subject)
                  ->markdown('emails.orders.confirmed');
    }
}
