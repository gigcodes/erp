<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiveChatEventLog extends Model
{
    protected $table = 'live_chat_event_logs';

    protected $fillable = ['id', 'customer_id', 'thread', 'event_type', 'log', 'store_website_id'];
}
