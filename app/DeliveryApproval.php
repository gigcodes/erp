<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class DeliveryApproval extends Model
{
  use Mediable;

  public function voucher()
  {
    return $this->hasOne('App\Voucher');
  }
}
