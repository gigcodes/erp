<?php

namespace App\Events;

use App\SendgridEvent;

/**
 * Class SendgridEventCreated.
 * The event that will be triggered each time Sendgrid send notification using webhook.
 */
class SendgridEventCreated
{
    /** @var SendgridEvent */
    private $sendgridEvent;

    /**
     * SendgridEventCreated constructor.
     */
    public function __construct(SendgridEvent $sendgridEvent)
    {
        $this->sendgridEvent = $sendgridEvent;
    }

    public function getSendgridEvent(): SendgridEvent
    {
        return $this->sendgridEvent;
    }

    public function getEventType(): string
    {
        return $this->getSendgridEvent()->event;
    }
}
