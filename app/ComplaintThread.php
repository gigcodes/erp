<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComplaintThread extends Model
{
  protected $fillable = [
    'complaint_id', 'account_id', 'thread'
  ];

  public function account()
  {
    return $this->belongsTo('App\Account');
  }
}
