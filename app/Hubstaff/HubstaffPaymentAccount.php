<?php

namespace App\Hubstaff;

use Illuminate\Database\Eloquent\Model;

class HubstaffPaymentAccount extends Model
{
    protected $fillable = [
        'user_id',
        'accounted_at',
        'amount'
    ];
}
