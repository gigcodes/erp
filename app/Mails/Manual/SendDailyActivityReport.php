<?php

namespace App\Mails\Manual;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendDailyActivityReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user;

    public $time_slots;

    public $fromMailer;

    public function __construct(User $user, array $time_slots)
    {
        $this->user = $user;
        $this->time_slots = $time_slots;
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
                    // ->bcc($this->fromMailer)
                    ->subject('Daily Planner Report')
                    ->markdown('emails.daily-activity-report');
    }
}
