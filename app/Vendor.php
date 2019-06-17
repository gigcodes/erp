<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
  protected $fillable = [
    'category_id', 'name', 'address', 'phone', 'default_phone', 'whatsapp_number', 'email', 'social_handle', 'website', 'login', 'password', 'gst'
  ];

  public function products()
  {
    return $this->hasMany('App\VendorProduct');
  }

  public function agents()
  {
    return $this->hasMany('App\Agent', 'model_id')->where('model_type', 'App\Vendor');
  }

  public function category()
  {
    return $this->belongsTo('App\VendorCategory');
  }
}
