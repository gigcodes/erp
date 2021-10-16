<?php

namespace App\Mails\Manual;

use App\Tickets;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketCreate extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $ticket;

    public function __construct(Tickets $ticket)
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

        $subject = "New Ticket # " . $this->ticket->ticket_id;
        $ticket = $this->ticket;
        $customer = $ticket->customer;

        $this->subject = $subject;
        $this->fromMailer = "customercare@sololuxury.co.in";

        $template = \App\MailinglistTemplate::getTicketCreateTemplate();

        if ($template) {
            if ($template->from_email != '') {
                $this->fromMailer = $template->from_email;
            }

            if (!empty($template->mail_tpl)) {
                // need to fix the all email address
                return $this->from($this->fromMailer)
                    ->subject($template->subject)
                    ->view($template->mail_tpl, compact(
                        'ticket', 'customer'
                    ));
            } else {
                $content = $template->static_template;
                return $this->from($this->fromMailer)
                    ->subject($template->subject)
                    ->view('emails.blank_content', compact(
                        'ticket', 'customer', 'content'
                    ));
            }
        }

        return $this->view('emails.tickets.created', compact(
            'ticket', 'customer'
        ));
    }
}
