<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReviewSchedule extends Model
{
  protected $fillable = [
    'account_id', 'date', 'posted_date', 'platform', 'review_count', 'review_link', 'status'
  ];

  public function reviews()
  {
    return $this->hasMany('App\Review', 'review_schedule_id');
  }

  public function account()
  {
    return $this->belongsTo('App\Account');
  }
}
