<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
  use SoftDeletes;
  protected $appends = ['communication', 'lead', 'order'];

  public function leads()
  {
    return $this->hasMany('App\Leads');
  }

  public function orders()
  {
    return $this->hasMany('App\Order');
  }

  public function instructions()
  {
    return $this->hasMany('App\Instruction');
  }

  public function latest_order()
  {
    return $this->hasMany('App\Order')->latest()->first();
  }

  public function messages()
	{
		return $this->hasMany('App\Message', 'moduleid')->where('moduletype', 'order')->latest()->first();
	}

	public function whatsapps()
	{
		return $this->hasMany('App\ChatMessage', 'customer_id')->latest()->first();
	}

	public function getCommunicationAttribute()
	{
		// $message = $this->messages();
		$whatsapp = $this->whatsapps();

		// if ($message && $whatsapp) {
		// 	if ($message->created_at > $whatsapp->created_at) {
		// 		return $message;
		// 	}
    //
		// 	return $whatsapp;
		// }

		// if ($message) {
		// 	return $message;
		// }

		return $whatsapp;
	}

  public function getLeadAttribute()
	{
		return $this->leads()->latest()->first();
	}

  public function getOrderAttribute()
	{
		return $this->orders()->latest()->first();
	}
}
