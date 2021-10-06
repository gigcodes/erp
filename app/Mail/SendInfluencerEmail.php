<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendInfluencerEmail extends Mailable
{
    use Queueable, SerializesModels;
	public $body;
	public $subject;
    public $fromMailer;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subject =  isset($data['subject']) ? $data['subject'] : "";
		$this->body =  isset($data['template']) ? $data['template'] : "";
        if(isset($data['from'])){
             $this->fromMailer =  $data['from'];
        }else{
            $this->fromMailer = 'customercare@sololuxury.co.in';  
        }
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
                    ->subject($this->subject)
                    ->html($this->body, 'text/html');
    }
}
