<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
  protected $fillable = [
    'first_name', 'last_name', 'email', 'password', 'dob', 'platform', 'followers_count', 'posts_count', 'dp_count'
  ];
}
