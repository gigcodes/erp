<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
  protected $fillable = [
    'user_id', 'delivery_approval_id', 'category_id', 'description', 'travel_type', 'amount', 'paid', 'date'
  ];

  public function user()
  {
    return $this->belongsTo('App\User');
  }

  public function category()
  {
    return $this->belongsTo('App\VoucherCategory');
  }
}
