<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
  protected $fillable = [
    'user_id', 'name', 'filename'
  ];

  public function user()
  {
    return $this->belongsTo('App\User');
  }
}
