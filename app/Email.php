<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
  protected $fillable = [
    'model_id', 'model_type', 'type', 'seen', 'from', 'to', 'subject', 'message', 'template', 'additional_data', 'created_at',
      'cc', 'bcc','origin_id','reference_id'
  ];

  protected $casts = [
    'cc' => 'array',
    'bcc' => 'array',
  ];


  public function model()
  {
  	return $this->morphTo();
  }
}
