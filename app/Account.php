<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
  protected $fillable = [
    'first_name', 'last_name', 'email', 'password', 'dob', 'platform', 'followers_count', 'posts_count', 'dp_count', 'broadcast', 'country', 'gender'
  ];

  public function reviews()
  {
    return $this->hasMany('App\Review');
  }

  public function has_posted_reviews()
  {
    $count = $this->hasMany('App\Review')->where('status', 'posted')->count();

    return $count > 0;
  }
}
