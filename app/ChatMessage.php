<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    //
    protected $fillable = ['lead_id', 'order_id', 'message', 'number'];
	protected $table ="chat_messages";
	protected $dates = ['created_at', 'updated_at'];
}
