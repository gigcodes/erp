<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallHistory extends Model
{
  protected $fillable = ['customer_id', 'status'];

  public function customer() {
    return $this->belongsTo('App\Customer');
  }
  public function store_website(){
      return $this->belongsTo(StoreWebsite::class);
  }
}
