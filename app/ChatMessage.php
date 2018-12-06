<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    //
    protected $fillable = ['lead_id', 'order_id', 'message', 'media_url', 'number', 'approved'];
	protected $table ="chat_messages";
	protected $dates = ['created_at', 'updated_at'];
    protected $casts = array(
        "approved" => "boolean"
    );
}
