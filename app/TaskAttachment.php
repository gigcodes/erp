<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
  protected $fillable = [
    'task_id','name'
  ];
}
