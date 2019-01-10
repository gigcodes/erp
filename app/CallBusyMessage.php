<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallBusyMessage extends Model
{
    protected $fillable = ['lead_id', 'twilio_call_sid' ,'message', 'recording_url', 'status'];
	protected $table ="call_busy_messages";
	protected $dates = ['created_at', 'updated_at'];
}
