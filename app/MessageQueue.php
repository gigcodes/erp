<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageQueue extends Model
{
  protected $fillable = [
    'user_id', 'customer_id', 'phone', 'type', 'data', 'sending_time', 'group_id'
  ];

  public function customer()
  {
    return $this->belongsTo('App\Customer');
  }

  public function chat_message()
  {
    return $this->belongsTo('App\ChatMessage');
  }
}
