<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioCallJourney extends Model
{
	protected $table = "twilio_call_journey";
    protected $fillable = ['account_sid', 'call_sid', 'phone', 'call_entered', 'called_in_working_hours', 'agent_available', 'agent_online','call_answered','handled_by_chatbot'];
}