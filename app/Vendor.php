<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
  protected $fillable = [
    'name', 'address', 'phone', 'email', 'social_handle', 'gst'
  ];

  public function products()
  {
    return $this->hasMany('App\VendorProduct');
  }
}
