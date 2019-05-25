<?php

namespace App;

use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use Mediable;
    protected $fillable = ['lead_id', 'order_id', 'customer_id', 'supplier_id', 'user_id', 'task_id', 'erp_user', 'dubbizle_id', 'assigned_to', 'purchase_id', 'message', 'media_url', 'number', 'approved', 'status', 'error_status', 'created_at'];
	protected $table ="chat_messages";
	protected $dates = ['created_at', 'updated_at'];
    protected $casts = array(
        "approved" => "boolean"
    );

    public function customer()
  	{
  		return $this->belongsTo('App\Customer');
  	}

    public function is_sent_broadcast_price()
  	{
  		$count = $this->hasMany('App\CommunicationHistory', 'model_id')->where('model_type', 'App\ChatMessage')->where('type', 'broadcast-prices')->count();

  		return $count > 0 ? TRUE : FALSE;
  	}
}
