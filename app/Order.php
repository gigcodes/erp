<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model {

	use SoftDeletes;

	protected $fillable = [
		'order_id',
		'customer_id',
		'order_type',
		'order_date',
		'awb',
		'client_name',
		'city',
		'contact_detail',
		'solophone',
		'advance_detail',
		'advance_date',
		'balance_amount',
		'sales_person',
		'office_phone_number',
		'order_status',
		'estimated_delivery_date',
		'note_if_any',

		'date_of_delivery',
		'received_by',
		'payment_mode',
		'auto_messaged',
		'auto_messaged_date',
		'auto_emailed',
		'auto_emailed_date',
		'remark',
        'whatsapp_number',
		'user_id',
		'is_priority'
	];

	protected $appends = ['action'];
	// protected $communication = '';
	// protected $action = '';

	public function order_product(){

		return $this->hasMany(OrderProduct::class,'order_id','id');

	}

	public function customer()
	{
		return $this->belongsTo('App\Customer');
	}

	public function Comment(){

		return $this->hasMany(Comment::class ,'subject_id','id')
		            ->where('subject_type','=',Order::class);
	}

	public function messages()
	{
		return $this->hasMany('App\Message', 'moduleid')->where('moduletype', 'order')->latest()->first();
	}

	public function reports()
	{
		return $this->hasMany('App\OrderReport', 'order_id')->latest()->first();
	}

	public function status_changes()
	{
		return $this->hasMany('App\StatusChange', 'model_id')->where('model_type', 'App\Order')->latest();
	}

	public function many_reports()
	{
		return $this->hasMany('App\OrderReport', 'order_id')->latest();
	}

	public function whatsapps()
	{
		return $this->hasMany('App\ChatMessage', 'order_id')->latest()->first();
	}

	public function delivery_approval()
	{
		return $this->hasOne('App\DeliveryApproval');
	}

	public function waybill()
	{
		return $this->hasOne('App\Waybill');
	}

	public function is_sent_initial_advance()
	{
		$count = $this->hasMany('App\CommunicationHistory', 'model_id')->where('model_type', 'App\Order')->where('type', 'initial-advance')->count();

		return $count > 0 ? TRUE : FALSE;
	}

	public function is_sent_advance_receipt()
	{
		$count = $this->hasMany('App\CommunicationHistory', 'model_id')->where('model_type', 'App\Order')->where('type', 'advance-receipt')->count();

		return $count > 0 ? TRUE : FALSE;
	}

	public function is_sent_online_confirmation()
	{
		$count = $this->hasMany('App\CommunicationHistory', 'model_id')->where('model_type', 'App\Order')->where('type', 'online-confirmation')->count();

		return $count > 0 ? TRUE : FALSE;
	}

	public function is_sent_refund_initiated()
	{
		$count = $this->hasMany('App\CommunicationHistory', 'model_id')->where('model_type', 'App\Order')->where('type', 'refund-initiated')->count();

		return $count > 0 ? TRUE : FALSE;
	}

	public function is_sent_offline_confirmation()
	{
		$count = $this->hasMany('App\CommunicationHistory', 'model_id')->where('model_type', 'App\Order')->where('type', 'offline-confirmation')->count();

		return $count > 0 ? TRUE : FALSE;
	}

	public function is_sent_order_delivered()
	{
		$count = $this->hasMany('App\CommunicationHistory', 'model_id')->where('model_type', 'App\Order')->where('type', 'order-delivered')->count();

		return $count > 0 ? TRUE : FALSE;
	}

	// public function getCommunicationAttribute()
	// {
	// 	$message = $this->messages();
	// 	$whatsapp = $this->whatsapps();
	//
	// 	if ($message && $whatsapp) {
	// 		if ($message->created_at > $whatsapp->created_at) {
	// 			return $message;
	// 		}
	//
	// 		return $whatsapp;
	// 	}
	//
	// 	if ($message) {
	// 		return $message;
	// 	}
	//
	// 	return $whatsapp;
	// }

	public function getActionAttribute()
	{
		return $this->reports();
	}
}
