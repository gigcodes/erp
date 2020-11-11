<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendReferralMail extends Mailable
{
    use Queueable, SerializesModels;
    public $referlink;
    public $referrer_email;
    public $referee_coupon;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->referlink = isset($data['referlink']) ? : "";
        $this->referrer_email = isset($data['referrer_email']) ?: "";
        $this->referee_coupon = isset($data['referee_coupon']) ? : "";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Refer A friend - Luxury Erp')->view('emails.referralprograms.referee_email');
    }
}
