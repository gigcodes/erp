<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;

class Issue extends Model
{
  use Mediable;
  use SoftDeletes;

  protected $fillable = [
    'user_id', 'issue', 'priority'
  ];
}
