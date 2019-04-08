<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReviewSchedule extends Model
{
  protected $fillable = [
    'date', 'platform', 'review_count', 'status'
  ];

  public function reviews()
  {
    return $this->hasMany('App\Review', 'review_schedule_id');
  }
}
