<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{

    protected $fillable = [
		'order_id',
	    'sku',
	    'product_price',
	    'size',
	    'color',
	    'qty',
    ];

    protected $appends = ['communication'];

	public function order(){

		return $this->belongsTo('App\Order','order_id','id');
	}

	public function product(){

//		return $this->hasOne('App\Product',['sku','color'],['sku','color']);
		return $this->hasOne('App\Product','sku','sku');

	}

  public function messages()
	{
		return $this->hasMany('App\Message', 'moduleid', 'order_id')->where('moduletype', 'order')->latest()->first();
	}

	public function whatsapps()
	{
		return $this->hasMany('App\ChatMessage', 'order_id', 'order_id')->latest()->first();
	}

  public function getCommunicationAttribute()
	{
		$message = $this->messages();
		$whatsapp = $this->whatsapps();

		if ($message && $whatsapp) {
			if ($message->created_at > $whatsapp->created_at) {
				return $message;
			}

			return $whatsapp;
		}

		if ($message) {
			return $message;
		}

		return $whatsapp;
	}
}
