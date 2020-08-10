<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioCallForwarding extends Model
{
    protected $table = 'twilio_call_forwarding';

    protected $fillable = ['twilio_number_sid','twilio_number','forwarding_on'];

}
