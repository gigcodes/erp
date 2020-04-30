<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturnExchangeHistory extends Model
{
    protected $fillable = [
        'return_exchange_id',
        'status_id',
        'user_id',
        'comment',
    ];
}
