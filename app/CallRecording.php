<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallRecording extends Model
{
    //
    protected $fillable = ['lead_id', 'recording_url', 'twilio_call_sid'];
	protected $table ="call_recordings";
	protected $dates = ['created_at', 'updated_at'];
}
