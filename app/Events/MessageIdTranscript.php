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
        \Log::info("Message event called => ". json_encode($event));
      
        $messageId = $event->message->getId();
        $to = $event->message->getTo();
        $bcc = $event->message->getBcc();
        $cc = $event->message->getCc();
        $from = $event->message->getFrom();
        $subject = $event->message->getSubject();
        $message = $event->message->getBody();

        $emailData = new Email();
        $emailData->origin_id = (string)$messageId ;
        foreach ($to as $key => $value) {
           $toValue[] = $key;
        }

        foreach ($from as $key => $value) {
            $fromValue[] = $key;
         }
       
        $emailData->to = json_encode($toValue);
        $emailData->from = json_encode($fromValue);
        $emailData->subject = (string)$subject;
        $emailData->message = (string)$message;
        // $emailData->cc = json_decode($cc,true);
        // $emailData->bcc = json_decode($bcc,true);
        $emailData->template = "default";
        
        $emailData->save();
 
    }

}