<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model {

	use SoftDeletes;

	protected $fillable = [
		'order_id',
		'order_type',
		'order_date',
		'client_name',
		'city',
		'contact_detail',
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
		'remark',
        'whatsapp_number',
		'user_id',
	];

	protected $appends = ['communication', 'action'];
	// protected $communication = '';
	// protected $action = '';

	public function order_product(){

		return $this->hasMany(OrderProduct::class,'order_id','id');

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

	public function getCommunicationAttribute()
	{
		return $this->messages();
	}

	public function getActionAttribute()
	{
		return $this->reports();
	}
}
