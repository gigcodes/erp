<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
  protected $fillable = [
    'user_id', 'cash_flow_category_id', 'description', 'date', 'amount', 'type'
  ];

  public function user()
  {
    return $this->belongsTo('App\User');
  }

  public function files()
  {
    return $this->hasMany('App\File', 'model_id')->where('model_type', 'App\CashFlow');
  }
}
