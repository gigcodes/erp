<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
  protected $fillable = [
    'customer_id', 'platform', 'complaint', 'link', 'date'
  ];

  public function customer()
  {
    return $this->belongsTo('App\Customer');
  }
}
