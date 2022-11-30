<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioCondition extends Model
{
    protected $table = 'twilio_conditions';

    protected $fillable = [
        'condition',
        'description',
        'status',
    ];
}
