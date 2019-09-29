<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeveloperTaskComment extends Model
{
  protected $fillable = [
    'task_id','user_id', 'comment'
  ];
}
