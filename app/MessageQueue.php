<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageQueue extends Model
{
  protected $fillable = [
    'user_id', 'customer_id', 'phone', 'type', 'data', 'sending_time'
  ];
}
