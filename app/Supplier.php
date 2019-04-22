<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
  protected $fillable = [
    'supplier', 'address', 'phone', 'email', 'social_handle', 'gst'
  ];

  public function agents()
  {
    return $this->hasMany('App\Agent', 'model_id')->where('model_type', 'App\Supplier');
  }
}
