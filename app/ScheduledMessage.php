<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduledMessage extends Model
{
  protected $fillable = [
    'user_id', 'customer_id', 'message', 'type', 'data', 'sending_time'
  ];
}
