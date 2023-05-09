<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    //
    protected $fillable = ['email_id', 'email_log', 'message', 'is_error', 'service_type'];
}
