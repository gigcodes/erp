<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    const STORE_ERP_WEBSITE = 15;

    public $body;

    public $subject;

    public $sendFrom;

    /**
     * Create a new message instance.
     *
     * @param mixed $data
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->subject = isset($data['subject']) ?: '';
        $this->body    = isset($data['static_template']) ?: '';
        if (isset($data['from'])) {
            $this->sendFrom = $data['from'];
        } else {
            $emailAddress = \App\EmailAddress::where('store_website_id', self::STORE_ERP_WEBSITE)->first();
            if ($emailAddress) {
                $this->sendFrom = $emailAddress->from_address;
            }
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
