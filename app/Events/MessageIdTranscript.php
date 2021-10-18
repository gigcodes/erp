<?php
namespace App\Events;
use App\Email;

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
        $emailModel = @$event->data['email'];
        if(isset($emailModel)) {
            $emailModel->origin_id = (string)$event->message->getId();
            $emailModel->save();
        }
    }

}