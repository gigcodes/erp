<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
  protected $fillable = [
    'account_id', 'customer_id', 'posted_date', 'review_link'
  ];

  public function review_schedule()
  {
    return $this->belongsTo('App\ReviewSchedule');
  }

  public function account()
  {
    return $this->belongsTo('App\Account');
  }

  public function customer()
  {
    return $this->belongsTo('App\Customer');
  }
}
