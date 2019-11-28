<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailAddress extends Model
{
  
  protected $fillable = [
    'from_name',
    'from_address',
    'driver',
    'host',
    'port',
    'encryption',
    'username',
    'password',
  ];

}
