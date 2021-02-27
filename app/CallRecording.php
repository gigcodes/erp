<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallRecording extends Model
{
    //
    protected $fillable = ['lead_id', 'order_id', 'customer_id', 'recording_url', 'twilio_call_sid' , 'customer_number','callsid', 'message'];
	protected $table ="call_recordings";
	protected $dates = ['created_at', 'updated_at'];
}
