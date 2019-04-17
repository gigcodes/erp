<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunicationHistory extends Model
{
  protected $fillable = [
    'model_id', 'model_type', 'type', 'method', 'created_at'
  ];
}
