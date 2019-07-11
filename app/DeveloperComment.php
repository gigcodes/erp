<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeveloperComment extends Model
{
  protected $fillable = [
    'user_id', 'send_to', 'message', 'status'
  ];
}
