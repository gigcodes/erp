<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReturnExchangeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $body;

    public $subject;

    public $sendFrom;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subject = isset($data['subject']) ?: '';
        $this->body = isset($data['static_template']) ?: '';
        if (isset($data['from'])) {
            $this->sendFrom = $data['from'];
        } else {
            $this->sendFrom = 'customercare@sololuxury.co.in';
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->sendFrom)
                    ->bcc($this->sendFrom)
                    ->subject($this->subject)
                    ->html($this->body, 'text/html');
    }
}
