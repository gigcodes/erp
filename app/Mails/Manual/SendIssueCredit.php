<?php

namespace App\Mails\Manual;

use App\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendIssueCredit extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
        $this->fromMailer = 'customercare@sololuxury.co.in';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $subject  = "Customer Credit Issued";
        $customer = $this->customer;

        if ($customer) {
            if ($customer->store_website_id > 0) {
                $template = \App\MailinglistTemplate::getIssueCredit($customer->store_website_id);
            } else {
                $template = \App\MailinglistTemplate::getIssueCredit(1);
            }
            if ($template) {
                if (!empty($template->mail_tpl)) {
                    // need to fix the all email address
                    return $this->subject($template->subject)
                        ->view($template->mail_tpl, compact(
                            'customer'
                        ));
                }
            }
        }

        return $this->subject($subject)->markdown('emails.customers.issue-credit');
    }
}
