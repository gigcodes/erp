<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
  protected $fillable = [
    'user_id', 'description', 'amount', 'date'
  ];

  public function user()
  {
    return $this->belongsTo('App\User');
  }
}
