<?php

namespace App\Mails\Manual;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HourlyReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $path;

    public $fromMailer;

    public function __construct($path)
    {
        $this->path       = $path;
        $this->fromMailer = \App\Helpers::getFromEmail();
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
                    ->subject('Generated Hourly Report')
                    ->markdown('emails.hourly-report')
                    ->attachFromStorageDisk('files', $this->path);
    }
}
