<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturnExchange extends Model
{

    //  use LogsActivity;
    use Mediable;

    protected $fillable = [
        'customer_id',
        'type',
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
}
