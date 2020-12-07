<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturnExchangeStatus extends Model
{
    const STATUS_TEMPLATE = 'Greetings from Solo Luxury Ref: number #{id} we have updated with status : #{status}.';

    protected $fillable = [
		'status_name',
        'message'
    ];
}
