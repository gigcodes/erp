<?php

namespace App;

use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class BroadcastImage extends Model
{
  use Mediable;

  protected $fillable = [
    'sending_time'
  ];
}
