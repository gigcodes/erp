<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PurchaseExport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $path;

    public function __construct(string $path)
    {
      $this->path = $path;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      return $this->from('contact@sololuxury.co.in')
                  ->cc('customercare@sololuxury.co.in')
                  ->subject('Purchases Export')
                  ->markdown('emails.purchases.export')
                  ->attachFromStorageDisk('uploads', $this->path);
    }
}
