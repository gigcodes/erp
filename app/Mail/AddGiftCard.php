<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddGiftCard extends Mailable
{
    use Queueable, SerializesModels;
    public $receiver_email;
    public $sender_email;
    public $coupon;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->receiver_email = $data['receiver_email'];
        $this->sender_email = $data['sender_email'];
        $this->coupon = $data['coupon'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Giftcard Recieved - Luxury Erp')->view('emails.giftcard.giftcard_recieved_email');
    }
}
