<?php

namespace App;

use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use Mediable;
    protected $fillable = ['lead_id', 'order_id', 'customer_id', 'user_id', 'message', 'media_url', 'number', 'approved', 'status'];
	protected $table ="chat_messages";
	protected $dates = ['created_at', 'updated_at'];
    protected $casts = array(
        "approved" => "boolean"
    );

    public function customer()
  	{
  		return $this->belongsTo('App\Customer');
  	}
}
