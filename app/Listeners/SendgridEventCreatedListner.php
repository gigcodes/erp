<?php

namespace App\Listeners;

use App\Events\SendgridEventCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendgridEventCreatedListner
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SendgridEventCreated  $event
     * @return void
     */
    public function handle(SendgridEventCreated $event)
    {
        $eventType = $sendgridEventCreated->getEventType(); //click, open...

        /**
         * ...
         */
        
        $sendgridEvent = $sendgridEventCreated->getSendgridEvent();
        $sendgridEvent->email;
        $sendgridEvent->timestamp;
        
    }
}
