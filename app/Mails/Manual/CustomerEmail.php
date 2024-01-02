<?php

namespace App\Mails\Manual;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerEmail extends Mailable
{
    use Queueable, SerializesModels;

    const STORE_ERP_WEBSITE = 15;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $subject;

    public $message;

    public $fromEmail;

    public function __construct(string $subject, string $message, string $fromStoreEmail)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->fromEmail = $fromStoreEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (! $this->fromEmail) {
            $emailAddress = \App\EmailAddress::where('store_website_id', self::STORE_ERP_WEBSITE)->first();
            if ($emailAddress) {
                $this->fromEmail = $emailAddress->from_address;
            }
        }

        return $this->from($this->fromEmail)
            ->bcc($this->fromEmail)
            ->subject($this->subject)
            ->markdown('emails.customers.email');
    }
}
