<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioWorker extends Model
{
    //
    protected $table = 'twilio_workers';

    protected $fillable = ['twilio_credential_id', 'twilio_workspace_id', 'worker_name', 'worker_sid', 'twilio_workers', 'deleted'];
}
