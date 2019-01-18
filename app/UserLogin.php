<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
  protected $fillable = [
    'user_id', 'login_at', 'logout_at'
  ];

  public function user() {
    return $this->belongsTo('App\User');
  }
}
