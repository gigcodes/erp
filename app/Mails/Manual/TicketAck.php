<?php

namespace App\Mails\Manual;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketAck extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $order;

    public function __construct($ticket)
    {
        $this->ticket = $ticket;
        $this->fromMailer = 'customercare@sololuxury.co.in';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject        = "Ticket ACK";
        $ticket          = $this->ticket;
                
        $this->subject  = $subject;
        $this->fromMailer = "customercare@sololuxury.co.in";


        $template = \App\MailinglistTemplate::getMailTemplate('Ticket ACK');

        if ($template) {
            if (!empty($template->mail_tpl)) {
                // need to fix the all email address
                return $this->from($this->fromMailer)
                    ->subject($template->subject)
                    ->view($template->mail_tpl, compact(
                        'ticket'
                    ));
            } else {
                $content = $template->static_template;
                return $this->from($this->fromMailer)
                    ->subject($template->subject)
                    ->view('emails.blank_content', compact(
                        'ticket', 'content'
                    ));
            }
        }
        
       
    }
}
