<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
  use SoftDeletes;

  // protected $appends = ['communication'];
	protected $communication = '';
  protected $fillable = ['whatsapp_number'];

	public function messages()
	{
		return $this->hasMany('App\Message', 'moduleid')->where('moduletype', 'purchase')->latest()->first();
	}

	// public function getCommunicationAttribute()
	// {
	// 	return $this->messages();
	// }

  public function products()
  {
    return $this->belongsToMany('App\Product', 'purchase_products', 'purchase_id', 'product_id');
  }

  public function files()
  {
    return $this->hasMany('App\File', 'model_id')->where('model_type', 'App\Purchase');
  }

  public function purchase_supplier()
  {
    return $this->belongsTo('App\Supplier', 'supplier_id');
  }

  public function agent()
  {
    return $this->belongsTo('App\Agent', 'agent_id');
  }

  public function emails()
  {
    return $this->hasMany('App\Email', 'model_id')->where('model_type', 'App\Purchase')->orWhere('model_type', 'App\Supplier');
  }

  public function status_changes()
	{
		return $this->hasMany('App\StatusChange', 'model_id')->where('model_type', 'App\Purchase')->latest();
	}
}
