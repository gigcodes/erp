<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusChange extends Model
{
  protected $fillable = [
    'model_id', 'model_type', 'user_id', 'from_status', 'to_status'
  ];
}
