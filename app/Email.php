<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
  protected $fillable = [
    'model_id', 'model_type', 'type', 'seen', 'from', 'to', 'subject', 'message', 'template', 'additional_data', 'created_at',
      'cc', 'bcc'
  ];

  protected $casts = [
    'cc' => 'array',
    'bcc' => 'array',
  ];



}
