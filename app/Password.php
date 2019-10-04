<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Password extends Model
{
  protected $fillable = [
    'website', 'url', 'username', 'password', "registered_with"
  ];
}
