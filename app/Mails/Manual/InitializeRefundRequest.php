<?php

namespace App\Mails\Manual;

use App\Customer;
use App\ReturnExchange;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InitializeRefundRequest extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $return;

    public function __construct(ReturnExchange $return)
    {
        $this->return = $return;
        $this->fromMailer = 'customercare@sololuxury.co.in';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $subject    = "Refund Initialized";
        $return     = $this->return;
        $customer   = $return->customer;

        if ($customer) {
            if ($customer->store_website_id > 0) {
                $template = \App\MailinglistTemplate::getIntializeRefund($customer->store_website_id);
            } else {
                $template = \App\MailinglistTemplate::getIntializeRefund(null);
            }
            if ($template) {
                if (!empty($template->mail_tpl)) {
                    // need to fix the all email address
                    return $this->subject($template->subject)
                        ->view($template->mail_tpl, compact(
                            'customer','return'
                        ));
                }
            }
        }

        return $this->subject($subject)->markdown('emails.customers.blank');
    }
}
