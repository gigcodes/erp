<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
  public function leads()
  {
    return $this->hasMany('App\Leads');
  }

  public function orders()
  {
    return $this->hasMany('App\Order');
  }
}
