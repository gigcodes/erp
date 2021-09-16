<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioActivity extends Model
{
    protected $table = 'twilio_activities';

    protected $fillable = ['twilio_credential_id', 'twilio_workspace_id', 'activity_name', 'availability', 'activity_sid', 'deleted'];
}
