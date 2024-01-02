<?php

namespace App\Events;

use Illuminate\Mail\Events\MessageSent;

class MessageIdTranscript
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(MessageSent $event)
    {
        $emailModel = @$event->data['email'];
        \Log::info('Json found here ' . json_encode([$event->message->getId(), $event->data]));
        if (isset($emailModel)) {
            $emailModel->origin_id = (string) $event->message->getId();
            if (isset($event->data['sg_message_id']) && $event->data['sg_message_id'] != '') {
                $emailModel->message_id = (string) $event->data['sg_message_id'];
            }
            $emailModel->save();
        }
    }
}
