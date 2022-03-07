<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiveChatLog extends Model
{
	protected $table = "customer_live_chats";
     protected $fillable = ['id','customer_id', 'thread', 'log'];
}
