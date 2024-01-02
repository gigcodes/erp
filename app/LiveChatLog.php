<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiveChatLog extends Model
{
    protected $table = 'live_chat_logs';

    protected $fillable = ['id', 'customer_id', 'thread', 'log'];
}
