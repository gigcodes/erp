<?php
namespace App\Events;

use Illuminate\Mail\Events\MessageSent;

class MessageIdTranscript {

    /**
     * Handle the event.
     *
     * @param  MessageSent  $event
     * @return void
     */
    public function handle(MessageSent $event)
    {
        $messageId = $event->message->getId();
 
    }

}