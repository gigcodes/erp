<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;
	
	public $body;
	public $subject;
	
	/**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
		$this->subject =  $data['subject'];
		$this->body =  $data['static_template'];
	}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		
		return $this->from('customercare@sololuxury.co.in')
                    ->bcc('customercare@sololuxury.co.in')
                    ->subject($this->subject)
                    ->html($this->body, 'text/html');
		
    }
}
