<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Plank\Mediable\Mediable;

class ReturnExchange extends Model
{

    use Mediable;

    protected $fillable = [
        'customer_id',
        'type',
        'reason_for_refund',
        'refund_amount',
        'status',
        'pickup_address',
        'remarks',
    ];

    const STATUS = [
        1 => 'Return request received from customer',
        2 => 'Return request sent to courier',
        3 => 'Return pickup',
        4 => 'Return received in warehouse',
        5 => 'Return accepted',
        6 => 'Return rejected'
    ];

    public function notifyToUser()
    {
        if($this->type == "refund") {
            // notify message we need to add here
        }
    }
}
