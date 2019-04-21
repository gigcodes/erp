<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
  use SoftDeletes;
  // protected $appends = ['communication'];
  protected $fillable = [
    'name', 'phone', 'city', 'whatsapp_number'
  ];

  public function leads()
  {
    return $this->hasMany('App\Leads');
  }

  public function orders()
  {
    return $this->hasMany('App\Order');
  }

  public function suggestion()
  {
    return $this->hasOne('App\Suggestion');
  }

  public function instructions()
  {
    return $this->hasMany('App\Instruction');
  }

  public function private_views()
  {
    return $this->hasMany('App\PrivateView');
  }

  public function latest_order()
  {
    return $this->hasMany('App\Order')->latest()->first();
  }

  public function many_reports()
	{
		return $this->hasMany('App\OrderReport', 'customer_id')->latest();
	}

  public function messages()
	{
		return $this->hasMany('App\Message', 'customer_id')->latest()->first();
	}

  public function messages_all()
	{
		return $this->hasMany('App\Message', 'customer_id')->latest();
	}

	public function whatsapps()
	{
		return $this->hasMany('App\ChatMessage', 'customer_id')->where('status', '!=', '7')->latest()->first();
	}

  public function call_recordings()
	{
		return $this->hasMany('App\CallRecording', 'customer_id')->latest();
	}

  public function whatsapps_all()
	{
		return $this->hasMany('App\ChatMessage', 'customer_id')->where('status', '!=', '7')->latest();
	}

	public function instagramThread() {
      return $this->hasOne(InstagramThread::class);
    }

  public function is_initiated_followup()
	{
		$count = $this->hasMany('App\CommunicationHistory', 'model_id')->where('model_type', 'App\Customer')->where('type', 'initiate-followup')->where('is_stopped', 0)->count();

		return $count > 0 ? TRUE : FALSE;
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

  public function getLeadAttribute()
	{
		return $this->leads()->latest()->first();
	}

  public function getOrderAttribute()
	{
		return $this->orders()->latest()->first();
	}
}
