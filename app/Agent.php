<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
  protected $fillable = [
    'model_id', 'model_type', 'name', 'phone', 'address', 'email'
  ];
}
