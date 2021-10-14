<?php

namespace App\Repositories;

use App\Email;
use App\SendgridEvent;

class SendgridEventRepository implements SendgridEventRepositoryInterface
{
    /** @var SendgridEvent */
    private $model;

    /**
     * SendgridEventRepository constructor.
     * @param SendgridEvent $sendgridEvent
     */
    public function __construct(SendgridEvent $sendgridEvent)
    {
        $this->model = $sendgridEvent;
    }

    /**
     * @inheritDoc
     */
    public function exists($sg_event_id): bool
    {
        return $this->model->newQuery()->where('sg_event_id', $sg_event_id)->exists();
    }

    /**
     * @inheritDoc
     */
    public function create($event): SendgridEvent
    {
        \Log::info('Send grid repo In');

        $newEvent = new SendgridEvent();
        $newEvent->timestamp = $event['timestamp'];
        $newEvent->email = $event['email'];
        $newEvent->event = $event['event'];
        $newEvent->sg_event_id = $event['sg_event_id'];
        $newEvent->sg_message_id = $event['sg_message_id'];
        $newEvent->payload = $event;

        if(isset($event['smtp-id'])) {
            $smptpID = str_replace(["<",">"],"",$event['smtp-id']);

            \Log::info('Send grid repo params defined');

            $emailData = new Email();
            if (Email::where('origin_id', '=', $smptpID)->exists()) {
                Email::where('origin_id', $smptpID)
                    ->update(['status' => $event['event']]
                    );
            }
        }

        \Log::info('Send grid repo email updated');

        if (!empty($event['category'])) {
            \Log::info('Send grid repo category In');
            $category = $event['category'];
            if (gettype($category) === "string") {
                \Log::info('Send grid repo category string');
                $newEvent->categories = [$category];
            } else {
                \Log::info('Send grid repo category not string');
                $newEvent->categories = $category;
            }
        }
        \Log::info('Send grid repo event save start');
        $newEvent->save();
        \Log::info('Send grid repo event save end');

        return $newEvent;
    }
}
