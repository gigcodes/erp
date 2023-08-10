<?php

namespace App\Mails\Manual;

use App\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IssueCredit extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $customer;

    public $fromMailer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
        $this->fromMailer = \App\Helpers::getFromEmail($this->customer->id);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->fromMailer)
                    ->bcc($this->fromMailer)
                    ->subject('Customer Credit Issued')
                    ->markdown('emails.customers.issue-credit');
    }
}
