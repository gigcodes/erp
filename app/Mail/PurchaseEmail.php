<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PurchaseEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $subject;
    public $message;
    public $file_paths;

    public function __construct(string $subject, string $message, array $file_paths)
    {
      $this->subject = $subject;
      $this->message = $message;
      $this->file_paths = $file_paths;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      if (count($this->file_paths) > 0) {
        $email = $this->from('buying@amourint.com')
                    ->bcc('customercare@sololuxury.co.in')
                    ->subject($this->subject)
                    ->text('emails.customers.email_plain')->with([
                      'body_message' => $this->message
                    ]);

        foreach ($this->file_paths as $file_path) {
          $email->attachFromStorageDisk('uploads', $file_path);
        }

        return $email;
      } else {
        return $this->from('buying@amourint.com')
                    ->bcc('customercare@sololuxury.co.in')
                    ->subject($this->subject)
                    ->text('emails.customers.email_plain')->with([
                      'body_message' => $this->message
                    ]);
      }
    }
}
