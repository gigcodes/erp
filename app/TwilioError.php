<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioError extends Model
{
    protected $table = 'twilio_errors';

    protected $fillable = ['sid', 'account_sid', 'call_sid', 'error_code', 'message_text', 'message_date', 'status'];
}
