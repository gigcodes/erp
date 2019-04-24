<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
  protected $fillable = [
    'model_id', 'model_type', 'from', 'to', 'subject', 'message', 'template', 'additional_data'
  ];
}
