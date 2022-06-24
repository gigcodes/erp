<?php


namespace App\Repositories;


use App\SendgridEvent;

interface SendgridEventRepositoryInterface
{
    /**
     * Check if event with given sendgrid event id exists in the database.
     *
     * @param $sg_event_id
     * @return bool
     */
    public function exists($sg_event_id,$email_id,$event);

    /**
     * Create new SendgridEvent using the given data.
     *
     * @param array $event
     * @return SendgridEvent
     */
    public function create($event): SendgridEvent;
}
