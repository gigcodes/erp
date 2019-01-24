<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeveloperTask extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'user_id', 'priority', 'task', 'cost', 'status', 'start_time', 'end_time'
  ];
}
