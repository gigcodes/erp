<?php

namespace App\Events;


use Illuminate\Queue\SerializesModels;
use App\SendgridEvent;

/**
 * Class SendgridEventCreated.
 * The event that will be triggered each time Sendgrid send notification using webhook.
 * @package LaravelSendgridEvents\Events
 */
class SendgridEventCreated
{
    /** @var SendgridEvent */
    private $sendgridEvent;

    /**
     * SendgridEventCreated constructor.
     * @param SendgridEvent $sendgridEvent
     */
    public function __construct(SendgridEvent $sendgridEvent)
    {
        $this->sendgridEvent = $sendgridEvent;
    }

    /**
     * @return SendgridEvent
     */
    public function getSendgridEvent(): SendgridEvent
    {
        return $this->sendgridEvent;
    }

    /**
     * @return string
     */
    public function getEventType(): string
    {
        return $this->getSendgridEvent()->event;
    }
}
