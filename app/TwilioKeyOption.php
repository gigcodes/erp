<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioKeyOption extends Model
{
    protected $table = 'twilio_key_options';

    protected $fillable = ['key', 'description', 'details', 'website_store_id', 'message'];
}
