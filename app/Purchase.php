<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
  use SoftDeletes;

  protected $appends = ['communication'];
	protected $communication = '';
  protected $fillable = ['whatsapp_number'];

	public function messages()
	{
		return $this->hasMany('App\Message', 'moduleid')->where('moduletype', 'purchase')->latest()->first();
	}

	public function getCommunicationAttribute()
	{
		return $this->messages();
	}

  public function products()
  {
    return $this->belongsToMany('App\Product', 'purchase_products', 'purchase_id', 'product_id');
  }

  public function files()
  {
    return $this->hasMany('App\File', 'model_id')->where('model_type', 'App\Purchase');
  }
}
