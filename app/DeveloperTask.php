<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;

class DeveloperTask extends Model
{
  use Mediable;
  use SoftDeletes;

  protected $fillable = [
    'user_id', 'module_id', 'priority', 'task', 'cost', 'status', 'module', 'start_time', 'end_time'
  ];
}
